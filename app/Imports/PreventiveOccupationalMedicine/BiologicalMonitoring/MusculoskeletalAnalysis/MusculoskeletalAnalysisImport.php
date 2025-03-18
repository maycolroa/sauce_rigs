<?php

namespace App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysisImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
use Exception;
use App\Traits\UtilsTrait;
use App\Traits\LocationFormTrait;

use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;

class MusculoskeletalAnalysisImport implements ToCollection
{
    use UtilsTrait, LocationFormTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $key_row = 2;
    private $sheet = 1;

    public function __construct($company_id, $user)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->keywords = $this->getKeywordQueue($this->company_id);
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {
            try
            {
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabeceras
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkRow($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de los Análisis Osteomuscular')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de Análisis Osteomuscular finalizo correctamente')
                        ->module('biologicalMonitoring/musculoskeletalAnalysis')
                        ->event('Job: MusculoskeletalAnalysisImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/analisis_osteomuscular_errors_'.date("YmdHis").'.xlsx';
                    Excel::store(new MusculoskeletalAnalysisImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de los Análisis Osteomuscular')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los Análisis Osteomuscular finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('biologicalMonitoring/musculoskeletalAnalysis')
                        ->event('Job: MusculoskeletalAnalysisImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de los Análisis Osteomuscular')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de los Análisis Osteomuscular. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('biologicalMonitoring/musculoskeletalAnalysis')
                    ->event('Job: MusculoskeletalAnalysisImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkRow($row)
    {
        $data = [];
        $saltos = 0;
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        if ($confLocation['regional'] == 'SI')
        {
            $data['regional'] = $row[0];
        }
        if ($confLocation['headquarter'] == 'SI')
        {
            $saltos = 1;
            $data['sede'] = $row[1];
        }
        if ($confLocation['process'] == 'SI')
        {
            $saltos = 2;
            $data['proceso'] = $row[2];

        }
        if ($confLocation['area'] == 'SI')
        {
            $saltos = 3;
            $data['area'] = $row[3];
        }

        $data = array_merge($data,
        [
            'patient_identification' => $row[1 + $saltos],
            'name' => $row[2 + $saltos],
            'evaluation_type' => $row[3 + $saltos],
            'evaluation_format' => $row[4 + $saltos],
            'company' => $row[5 + $saltos],
            'branch_office' => $row[6 + $saltos],
            'sex' => $row[7 + $saltos],
            'age' => $row[8 + $saltos],
            'phone' => $row[9 + $saltos],
            'phone_alternative' => $row[10 + $saltos],
            'eps' => $row[11 + $saltos],
            'afp' => $row[12 + $saltos],
            'position' => $row[13 + $saltos],
            'antiquity' => $row[14 + $saltos],
            'state' => $row[15 + $saltos],
            'ant_atep_ep' => $row[16 + $saltos],
            'which_ant_atep_ep' => $row[17 + $saltos],
            'exercise_habit' => $row[18 + $saltos],
            'exercise_frequency' => $row[19 + $saltos],
            'liquor_habit' => $row[20 + $saltos],
            'liquor_frequency' => $row[21 + $saltos],
            'exbebedor_habit' => $row[22 + $saltos],
            'liquor_suspension_time' => $row[23 + $saltos],
            'cigarette_habit' => $row[24 + $saltos],
            'cigarette_frequency' => $row[25 + $saltos],
            'habit_extra_smoker' => $row[26 + $saltos],
            'cigarrillo_suspension_time' => $row[27 + $saltos],
            'activity_extra_labor' => $row[28 + $saltos],
            'weight' => $row[29 + $saltos],
            'size' => $row[30 + $saltos],
            'imc' => $row[31 + $saltos],
            'imc_lassification' => $row[32 + $saltos],
            'abdominal_perimeter' => $row[33 + $saltos],
            'abdominal_perimeter_classification' => $row[34 + $saltos],
            'diagnostic_code_1' => $row[35 + $saltos],
            'diagnostic_1' => $row[36 + $saltos],
            'diagnostic_code_2' => $row[37 + $saltos],
            'diagnostic_2' => $row[38 + $saltos],
            'diagnostic_code_3' => $row[39 + $saltos],
            'diagnostic_3' => $row[40 + $saltos],
            'diagnostic_code_4' => $row[41 + $saltos],
            'diagnostic_4' => $row[42 + $saltos],
            'diagnostic_code_5' => $row[43 + $saltos],
            'diagnostic_5' => $row[44 + $saltos],
            'diagnostic_code_6' => $row[45 + $saltos],
            'diagnostic_6' => $row[46 + $saltos],
            'diagnostic_code_7' => $row[47 + $saltos],
            'diagnostic_7' => $row[48 + $saltos],
            'diagnostic_code_8' => $row[49 + $saltos],
            'diagnostic_8' => $row[50 + $saltos],
            'diagnostic_code_9' => $row[51 + $saltos],
            'diagnostic_9' => $row[52 + $saltos],
            'diagnostic_code_10' => $row[53 + $saltos],
            'diagnostic_10' => $row[54 + $saltos],
            'diagnostic_code_11' => $row[55 + $saltos],
            'diagnostic_11' => $row[56 + $saltos],
            'diagnostic_code_12' => $row[57 + $saltos],
            'diagnostic_12' => $row[58 + $saltos],
            'diagnostic_code_13' => $row[59 + $saltos],
            'diagnostic_13' => $row[60 + $saltos],
            'cardiovascular_risk' => $row[61 + $saltos],
            'osteomuscular_classification' => $row[62 + $saltos],
            'osteomuscular_group' => $row[63 + $saltos],
            'age_risk' => $row[64 + $saltos],
            'pathological_background_risks' => $row[65 + $saltos],
            'extra_labor_activities_risk' => $row[66 + $saltos],
            'sedentary_risk' => $row[67 + $saltos],
            'imc_risk' => $row[68 + $saltos],
            'consolidated_personal_risk_punctuation' => $row[69 + $saltos],
            'consolidated_personal_risk_criterion' => $row[70 + $saltos],
            'prioritization_medical_criteria' => $row[71 + $saltos],
            'concept' => $row[72 + $saltos],
            'recommendations' => $row[73 + $saltos],
            'observations' => $row[74 + $saltos],
            'restrictions' => $row[75 + $saltos],
            'remission' => $row[76 + $saltos],
            'description_medical_exam' => $row[77 + $saltos],
            'symptom' => $row[78 + $saltos],
            'symptom_type' => $row[79 + $saltos],
            'body_part' => $row[80 + $saltos],
            'periodicity' => $row[81 + $saltos],
            'workday' => $row[82 + $saltos],
            'symptomatology_observations' => $row[83 + $saltos],
            'id3' => $row[84 + $saltos]
        ]);
        
        $rules = [];

        if ($confLocation['regional'] == 'SI')
            $rules['regional'] = 'required';
        if ($confLocation['headquarter'] == 'SI')
            $rules['sede'] = 'required';
        if ($confLocation['process'] == 'SI')
            $rules['proceso'] = 'required';
        if ($confLocation['area'] == 'SI')
            $rules['area'] = 'required';

        $validator = Validator::make($data, $rules, 
        [
            'regional.required' => 'El campo '.$this->keywords['regional'].' es obligatorio.',
            'sede.required' => 'El campo '.$this->keywords['headquarter'].' es obligatorio.',
            'proceso.required' => 'El campo '.$this->keywords['process'].' es obligatorio.',
            'area.required' => 'El campo '.$this->keywords['area'].' es obligatorio.'

        ]);

        if ($validator->fails())
        {
            foreach ($validator->messages()->all() as $value)
            {
                $this->setError($value);
            }

            $this->setErrorData($row);

            return null;
        }
        else 
        {
            $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional']) : null;
            $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
            $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso']) : null; 
            $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

            $record = new MusculoskeletalAnalysis;
            $record->company_id = $this->company_id;
            $record->employee_regional_id = $regional_id;
            $record->employee_headquarter_id = $headquarter_id;
            $record->employee_area_id = $area_id;
            $record->employee_process_id = $process_id;
            $record->patient_identification = $data['patient_identification'];
            $record->name = $data['name'];
            $record->date = date('Y-m-d');
            $record->evaluation_type = $data['evaluation_type'];
            $record->evaluation_format = $data['evaluation_format'];
            $record->company = $data['company'];
            $record->branch_office = $data['branch_office'];
            $record->sex = $data['sex'];
            $record->age = $data['age'];
            $record->phone = $data['phone'];
            $record->phone_alternative = $data['phone_alternative'];
            $record->eps = $data['eps'];
            $record->afp = $data['afp'];
            $record->position = $data['position'];
            $record->antiquity = $data['antiquity'];
            $record->state = $data['state'];
            $record->ant_atep_ep = $data['ant_atep_ep'];
            $record->which_ant_atep_ep = $data['which_ant_atep_ep'];
            $record->exercise_habit = $data['exercise_habit'];
            $record->exercise_frequency = $data['exercise_frequency'];
            $record->liquor_habit = $data['liquor_habit'];
            $record->liquor_frequency = $data['liquor_frequency'];
            $record->exbebedor_habit = $data['exbebedor_habit'];
            $record->liquor_suspension_time = $data['liquor_suspension_time'];
            $record->cigarette_habit = $data['cigarette_habit'];
            $record->cigarette_frequency = $data['cigarette_frequency'];
            $record->habit_extra_smoker = $data['habit_extra_smoker'];
            $record->cigarrillo_suspension_time = $data['cigarrillo_suspension_time'];
            $record->activity_extra_labor = $data['activity_extra_labor'];
            $record->weight = $data['weight'];
            $record->size = $data['size'];
            $record->imc = $data['imc'];
            $record->imc_lassification = $data['imc_lassification'];
            $record->abdominal_perimeter = $data['abdominal_perimeter'];
            $record->abdominal_perimeter_classification = $data['abdominal_perimeter_classification'];
            $record->diagnostic_code_1 = $data['diagnostic_code_1'];
            $record->diagnostic_1 = $data['diagnostic_1'];
            $record->diagnostic_code_2 = $data['diagnostic_code_2'];
            $record->diagnostic_2 = $data['diagnostic_2'];
            $record->diagnostic_code_3 = $data['diagnostic_code_3'];
            $record->diagnostic_3 = $data['diagnostic_3'];
            $record->diagnostic_code_4 = $data['diagnostic_code_4'];
            $record->diagnostic_4 = $data['diagnostic_4'];
            $record->diagnostic_code_5 = $data['diagnostic_code_5'];
            $record->diagnostic_5 = $data['diagnostic_5'];
            $record->diagnostic_code_6 = $data['diagnostic_code_6'];
            $record->diagnostic_6 = $data['diagnostic_6'];
            $record->diagnostic_code_7 = $data['diagnostic_code_7'];
            $record->diagnostic_7 = $data['diagnostic_7'];
            $record->diagnostic_code_8 = $data['diagnostic_code_8'];
            $record->diagnostic_8 = $data['diagnostic_8'];
            $record->diagnostic_code_9 = $data['diagnostic_code_9'];
            $record->diagnostic_9 = $data['diagnostic_9'];
            $record->diagnostic_code_10 = $data['diagnostic_code_10'];
            $record->diagnostic_10 = $data['diagnostic_10'];
            $record->diagnostic_code_11 = $data['diagnostic_code_11'];
            $record->diagnostic_11 = $data['diagnostic_11'];
            $record->diagnostic_code_12 = $data['diagnostic_code_12'];
            $record->diagnostic_12 = $data['diagnostic_12'];
            $record->diagnostic_code_13 = $data['diagnostic_code_13'];
            $record->diagnostic_13 = $data['diagnostic_13'];
            $record->cardiovascular_risk = $data['cardiovascular_risk'];
            $record->osteomuscular_classification = $data['osteomuscular_classification'];
            $record->osteomuscular_group = $data['osteomuscular_group'];
            $record->age_risk = $data['age_risk'];
            $record->pathological_background_risks = $data['pathological_background_risks'];
            $record->extra_labor_activities_risk = $data['extra_labor_activities_risk'];
            $record->sedentary_risk = $data['sedentary_risk'];
            $record->imc_risk = $data['imc_risk'];
            $record->consolidated_personal_risk_punctuation = $data['consolidated_personal_risk_punctuation'];
            $record->consolidated_personal_risk_criterion = $data['consolidated_personal_risk_criterion'];
            $record->prioritization_medical_criteria = $data['prioritization_medical_criteria'];
            $record->concept = $data['concept'];
            $record->recommendations = $data['recommendations'];
            $record->observations = $data['observations'];
            $record->restrictions = $data['restrictions'];
            $record->remission = $data['remission'];
            $record->description_medical_exam = $data['description_medical_exam'];
            $record->symptom = $data['symptom'];
            $record->symptom_type = $data['symptom_type'];
            $record->body_part = $data['body_part'];
            $record->periodicity = $data['periodicity'];
            $record->workday = $data['workday'];
            $record->symptomatology_observations = $data['symptomatology_observations'];
            $record->id3 = $data['id3'];
            $record->save();

            return true;
        }
    }

    private function setError($message)
    {
        $this->errors[$this->key_row][] = ucfirst($message);
    }

    private function setErrorData($row)
    {
        $this->errors_data[] = $row;
        $this->key_row++;
    }

    private function validateDate($date)
    {
        try
        {
            $d = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }
        catch (\Exception $e) {
            try
            {
                return Carbon::createFromFormat('d/m/Y h:i:s a', $date)->format('Y-m-d H:i:s');
            }
            catch (\Exception $e) {
                return null;
            }
        }

        return $d ? $d : null;
    }

    private function checkArea($headquarter_id, $process_id, $area_name)
    {        
        $area = EmployeeRegional::select("sau_employees_areas.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->join('sau_process_area', 'sau_process_area.employee_process_id', 'sau_employees_processes.id')
            ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_process_area.employee_area_id')
            ->where('sau_employees_areas.name', $area_name)
            ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name');

        $area->company_scope = $this->company_id;
        $area = $area->first();

        if (!$area)
        {
            $area = new EmployeeArea();
            $area->name = $area_name;
            $area->save();
        }
        else
            $area = EmployeeArea::find($area->id);
        
        $area->processes()->wherePivot('employee_headquarter_id','=',$headquarter_id)->detach($process_id);
        $area->processes()->attach($process_id, ['employee_headquarter_id' => $headquarter_id]);

        return $area->id;
    }

    private function checkRegional($name)
    {
        $regional = EmployeeRegional::query();
        $regional->company_scope = $this->company_id;
        $regional = $regional->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $regional->id;
    }

    private function checkHeadquarter($regional_id, $headquarter)
    {
        $headquarter = EmployeeHeadquarter::firstOrCreate(['name' => $headquarter, 'employee_regional_id' => $regional_id], 
                                            ['name' => $headquarter, 'employee_regional_id' => $regional_id]);

        return $headquarter->id;
    }

    private function checkProcess($headquarter_id, $process_name)
    {
        $process = EmployeeRegional::select("sau_employees_processes.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->where('sau_employees_processes.name', $process_name)
            ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name');

        $process->company_scope = $this->company_id;
        $process = $process->first();

        if (!$process)
        {
            $process = new EmployeeProcess();
            $process->name = $process_name;
            $process->save();
        }
        else
        {
            $process = EmployeeProcess::find($process->id);
            $process->save();
        }
        
        $process->headquarters()->detach($headquarter_id);
        $process->headquarters()->attach($headquarter_id);

        return $process->id;
    }
}