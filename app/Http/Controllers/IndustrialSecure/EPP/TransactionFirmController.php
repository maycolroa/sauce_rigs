<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Carbon\Carbon;
use App\Models\General\Company;
use DB;

class TransactionFirmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($transaction, $employee)
    {
        $errorMenssage = '';
        $data = collect([]);

        $employee = Employee::withoutGlobalScopes()->findOrFail($employee);

        if ($employee)
        {
            $delivery = ElementTransactionEmployee::withoutGlobalScopes()->findOrFail($transaction);

            if ($employee->company_id == $delivery->company_id)
            {
                if (!$delivery->firm_employee)
                {
                    $delivery->employee_name = $delivery->employee->name;
                    $delivery->employee_identification = $delivery->employee->identification;

                    $element_balance_id = [];
                    $elements = [];

                    foreach ($delivery->elements as $key => $value) 
                    {
                        if (!in_array($value->element_balance_id, $element_balance_id))
                        {
                            array_push($element_balance_id, $value->element_balance_id);
                        }                    
                    }

                    $ids_balance_saltar = [];

                    foreach ($element_balance_id as $key => $value) 
                    {
                        $element = $delivery->elements()->where('element_balance_id', $value)->get();

                        foreach ($element as $key => $e) 
                        {
                            if ($e->element->element->identify_each_element)
                            {
                                $content = [
                                    'quantity' => 1,
                                    'name' => $e->element->element->name
                                ];

                                array_push($elements, $content);
                            }
                            else
                            {
                                if (!in_array($e->element_balance_id, $ids_balance_saltar))
                                {
                                    $content = [
                                        'quantity' => $element->count(),
                                        'name' => $e->element->element->name
                                    ];

                                    array_push($elements, $content);
                                    array_push($ids_balance_saltar, $e->element_balance_id);
                                }
                            }
                            
                        }
                    }

                    $delivery->elements = $elements;
                    $delivery->user_name = $delivery->user_id ? $delivery->user->name : '';

                    $company = Company::select('logo', 'name')->where('id', $delivery->company_id)->first();

                    $logo = ($company && $company->logo) ? $company->logo : null;

                    $delivery->logo = $logo;

                    $delivery->text_company = $this->getTextLetterEpp($company);

                }
                else
                    $errorMenssage = 'El empleado ya ha firmado este documento';
            }
            else
                $errorMenssage = 'El empleado no tiene acceso a este documento';
        }
        else
            $errorMenssage = 'El empleado no exite';
      

        /*return view('legalAspects.trainingEmployee', [
            'errorMenssage' => $errorMenssage,
            'data' => $delivery
        ]);*/
    }

    public function getTextLetterEpp($company)
    {
        $text = ConfigurationCompany::select('value')->where('company_id', $company->id)->where('key', 'text_letter_epp')->first();

        $text_default = '<p>Yo, empleado (a) de '.$company->name .' hago constar que he recibido lo aquí relacionado y firmado por mí.  Doy fé además que he sido informado y capacitado en cuanto al uso de los elementos de protección personal y  frente a los riesgos que me protegen, las actividades y ocasiones en las cuales debo utilizarlos.  He sido informado sobre el procedimiento para su cambio y reposición en caso que sea necesario.

        *Me comprometo a hacer buen uso de todo lo recibido y a realizar el mantenimiento adecuado de los mismos.   Me comprometo a utilizarlos y cuidarlos conforme a las instrucciones recibidas y a la normativa legal vigente; así mismo me comprometo a informar a mi jefe inmediato cualquier defecto, anomalía o daño del elemento de protección personal (EPP) que pueda afectar o disminuir la efectividad de la protección.</p>';

        if (!$text)
            return $text_default;
        else
            return $text->value;
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

                if ($question['type']['name'] != 'selection_multiple')
                {
                    if ($answers == $answer->first())
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
}