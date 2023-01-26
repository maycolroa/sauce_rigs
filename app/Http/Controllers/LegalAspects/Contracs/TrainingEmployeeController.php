<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\LegalAspects\Contracts\TrainingEmployeeRequest;
use App\Models\LegalAspects\Contracts\Training;
use App\Models\LegalAspects\Contracts\TrainingQuestions;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\TrainingEmployeeSend;
use App\Models\LegalAspects\Contracts\TrainingEmployeeAttempt;
use App\Models\LegalAspects\Contracts\TrainingEmployeeQuestionsAnswers;
use App\Models\LegalAspects\Contracts\TrainingFiles;
use Carbon\Carbon;
use DB;

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

            $attempt = new TrainingEmployeeAttempt();
            $attempt->attempt = $request->attempt;
            $attempt->employee_id = $request->employee;
            $attempt->training_id = $request->id;
            $attempt->state = 'REPROBADO';
            $attempt->save();
            $grade = 0;

            foreach ($request->questions as $question) 
            {
                $questionAnswer = TrainingQuestions::find($question['id']);
                $answer = collect($questionAnswer->answer_options->get('answers'))->sort();

                $answersEmployee = new TrainingEmployeeQuestionsAnswers();
                $answersEmployee->question_id = $question['id'];
                $answersEmployee->attempt_id = $attempt->id;

                if (!is_array($question['answers']))
                    $answers = $question['answers'];
                else
                    $answers = $this->getValuesForMultiselect($question['answers'])->sort()->implode("|");

                $answersEmployee->answers = $answers;

                if ($question['type']['name'] != 'selection_multiple' && $question['type']['name'] != 'pairing')
                {
                    if ($answers == $answer->first())
                        $answersEmployee->correct = true;
                    else
                        $answersEmployee->correct = false;
                }
                else if ($question['type']['name'] == 'pairing')
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
}