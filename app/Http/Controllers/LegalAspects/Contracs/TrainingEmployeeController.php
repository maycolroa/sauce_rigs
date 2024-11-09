<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\General\Company;
use App\Http\Requests\LegalAspects\Contracts\TrainingEmployeeRequest;
use App\Models\LegalAspects\Contracts\Training;
use App\Models\LegalAspects\Contracts\TrainingQuestions;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\TrainingEmployeeSend;
use App\Models\LegalAspects\Contracts\TrainingEmployeeAttempt;
use App\Models\LegalAspects\Contracts\TrainingEmployeeQuestionsAnswers;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\TrainingFiles;
use Carbon\Carbon;
use DB;
use PDF;

class TrainingEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($training, $token)
    {
        $errorMenssage = '';
        $data = collect([]);

        $employee = ContractEmployee::withoutGlobalScopes()->where('token', $token)->first();

        if ($employee)
        {
            $training = Training::withoutGlobalScopes()->find($training);
            $training->files = $this->getFiles($training);

            if ($training)
            {
                if ($training->isActive())
                {
                    $trainingSend = TrainingEmployeeSend::
                          where('employee_id', $employee->id)
                        ->where('training_id', $training->id)
                        ->first();

                    if ($trainingSend)
                    {
                        $attempts = TrainingEmployeeAttempt::where('employee_id', $employee->id)
                            ->where('training_id', $training->id)
                            ->count();

                        if ($attempts >= $training->number_attemps)
                            $errorMenssage = 'Ha agotado el número de intentos para realizar esta capacitación';
                    }
                    else
                        $errorMenssage = 'El empleado no tiene asignada esta capacitación';
                }
                else
                    $errorMenssage = 'La capacitación a la cual desea ingresar no se encuentra activa en este momento';
            }
            else
                $errorMenssage = 'La capacitación a la cual desea ingresar no existe';
        }
        else
            $errorMenssage = 'El empleado no exite';

        if (!$errorMenssage)
        {
            $training->questions = $training->questions()->inRandomOrder()->limit($training->number_questions_show)->get()->transform(function($question, $key) {
                $question->type;
                $answers = $question->answer_options->except('options');
                $options = $question->answer_options->except('answers');
                $ans = $answers->get('answers');
                shuffle($ans);
                $options->put('options', $this->multiSelectFormat($options->get('options')));
                $options->put('answers', $this->multiSelectFormat($ans));
                $question->answer_options = $options;
                $question->answers_pairing = [];
                $question->answers = '';
                $question->key = Carbon::now()->timestamp + rand(1,10000);
                //\Log::info($question->answer_options);

                return $question;
            });

            $training->attempt = $attempts + 1;
            $training->employee = $employee->id;
            $training->firm_employee = '';
            $data->put("training", $training);
        }

        return view('legalAspects.trainingEmployee', [
            'errorMenssage' => $errorMenssage,
            'data' => $data->has('training') ? $data->get('training') : $data
        ]);
    }

    public function saveTraining(TrainingEmployeeRequest $request)
    {
        DB::beginTransaction();
        
        try
        {
            $training = Training::withoutGlobalScopes()->find($request->id);
            $imageName = NULL;

            if ($request->firm_employee)
            {
                $image_64 = $request->firm_employee;
        
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        
                $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
        
                $image = str_replace($replace, '', $image_64); 
        
                $image = str_replace(' ', '+', $image); 
        
                $imageName = base64_encode($request->employee . rand(1,10000) . now()) . '.' . $extension;

                $file = base64_decode($image);

                Storage::disk('s3')->put('legalAspects/contracts/trainings/files/'.$training->company_id.'/' . $imageName, $file, 'public');
            }

            $attempt = new TrainingEmployeeAttempt();
            $attempt->attempt = $request->attempt;
            $attempt->employee_id = $request->employee;
            $attempt->training_id = $request->id;
            $attempt->state = 'REPROBADO';
            $attempt->firm = $imageName;
            $attempt->save();
            $grade = 0;

            foreach ($request->questions as $question) 
            {
                $questionAnswer = TrainingQuestions::find($question['id']);
                $answer = collect($questionAnswer->answer_options->get('answers'))->sort();

                $answersEmployee = new TrainingEmployeeQuestionsAnswers();
                $answersEmployee->question_id = $question['id'];
                $answersEmployee->attempt_id = $attempt->id;

                if (is_array($question['answers_pairing']) && COUNT($question['answers_pairing']) > 0)
                    $answers = implode('|', $question['answers_pairing']);
                else if (!is_array($question['answers']))
                    $answers = $question['answers'];                
                else
                    $answers = $this->getValuesForMultiselect($question['answers'])->sort()->implode("|");

                $answersEmployee->answers = $answers;

                if ($question['type']['id'] != 3 && $question['type']['id'] != 5)
                {
                    if ($answers == $answer->first())
                        $answersEmployee->correct = true;
                    else
                        $answersEmployee->correct = false;
                }
                else if ($question['type']['id'] == 5)
                {
                    $answers_repeat = [];
                    $answers = $question['answers_pairing'];

                    foreach ($answers as $key => $value) 
                    {
                        if (!in_array($value, $answers_repeat))
                        {
                            array_push($answers_repeat, $value);
                        }
                        else
                        {
                            return $this->respondWithError('No puede seleccionar la misma respuesta en varias opciones, por favor revisar las respuestas en la pregunta '. $questionAnswer->description, 422);
                        }
                    }

                    $ans = [];


                    if (implode('|', $answers) == $answer->implode("|"))
                        $answersEmployee->correct = true;
                    else
                        $answersEmployee->correct = false;
                }
                else
                {
                    if ($answers == $answer->implode("|"))
                        $answersEmployee->correct = true;
                    else
                        $answersEmployee->correct = false;
                }

                $answersEmployee->save();
                $grade += $answersEmployee->correct ? $questionAnswer->value_question : 0;
            }

            if ($grade >= $training->min_calification)
                $attempt->update(['state' => 'APROBADO']);

            DB::commit();

            return $this->respondHttp200([
                'result' => $attempt->state
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $training = TrainingFiles::find($id);
        return Storage::disk('s3')->download($training->path_donwload());
    }

    public function getFiles($training)
    {
        $get_files = TrainingFiles::where('training_id', $training->id)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->name = $get_file->name;
                $get_file->type = $get_file->type;
                $get_file->link = $get_file->link;
                $get_file->old_name = $get_file->file;

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
    }

    public function dataEmployee(Request $request)
    {
        $training_employees = Training::select(
            'sau_ct_training_employee_attempts.id',
            'nit',
            'social_reason',
            'sau_ct_trainings.name as training',
            'sau_ct_contract_employees.name as employee',
            'attempt',
            'sau_ct_training_employee_attempts.state as state_attempts'
        )
        ->join('sau_ct_training_employee_attempts', 'sau_ct_training_employee_attempts.training_id', 'sau_ct_trainings.id')
        ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_training_employee_attempts.employee_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.contract_id')
        ->where('sau_ct_training_employee_attempts.training_id', $request->get('modelId'));

        return Vuetable::of($training_employees)
            ->addColumn('downloadFile', function ($training_employee) {
                if ($training_employee->state_attempts == 'APROBADO')
                    return true;

                return false;
            })
            ->make();
    }

    public function showEmployee($id, $export_pdf = false)
    {
        try
        {
            $attempt = TrainingEmployeeAttempt::findOrFail($id);

            $question_answer = TrainingEmployeeQuestionsAnswers::where('attempt_id', $attempt->id)->get();
            
            $employee = $attempt->employees;
            $attempt->employee = $employee;
            $attempt->contract = ContractLesseeInformation::find($employee->contract_id);

            foreach ($question_answer as $answer)
            {
                $question = TrainingQuestions::find($answer->question_id);

                $answer->key = Carbon::now()->timestamp + rand(1,10000);
                $answer->description = $question->description;
                $answer->type_question_id = $question->type_question_id;

                if ($question->type_question_id == 1 || $question->type_question_id == 3)
                {
                    $ans = explode('|', $answer->answers);
                    $answer->answer = implode(',', $ans);
                }
                else if ($question->type_question_id == 5)
                {
                    $paring = [];

                    $options = collect($question->answer_options->get('options'));

                    foreach ($options as $key => $value) {
                       array_push($paring, $value);
                    }

                    $ans = explode('|', $answer->answers);

                    foreach ($ans as $key => $value) {
                        $paring[$key] = $paring[$key].'-'.$value;
                    }

                    $answer->answer = implode(', ', $paring);
                }
                else
                {
                    $answer->answer = $answer->answers;
                }
                
            }

            $attempt->questions = $question_answer;
            $attempt->name = Training::find($attempt->training_id)->name;
            $attempt->qualification = $attempt->state;
            $attempt->path_firm = Storage::disk('s3')->url('legalAspects/contracts/trainings/files/'.$this->company.'/'.$attempt->firm);
            $attempt->date_approver = Carbon::parse($attempt->created_at)->format('d-m-Y');

            if ($export_pdf)
                return $attempt;
            else
            {
                return $this->respondHttp200([
                    'data' => $attempt
                ]);
            }

        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function downloadPdf($id)
    {
        $training = $this->showEmployee($id, true);
        $training->company = Company::find($this->company);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.trainingEmployeeCertificate', ['training' => $training] );

        //$pdf->setPaper('A3', 'landscape');

        return $pdf->download('certificado.pdf');
    }
}