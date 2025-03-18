<?php

namespace App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisImportErrorExcel;
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

class RespiratoryAnalysisImport implements ToCollection
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
                        subject('Importación de los Análisis Respiratorio')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de Análisis Respiratorio finalizo correctamente')
                        ->module('biologicalMonitoring/respiratoryAnalysis')
                        ->event('Job: RespiratoryAnalysisImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/analisis_Respiratorio_errors_'.date("YmdHis").'.xlsx';
                    Excel::store(new RespiratoryAnalysisImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de los Análisis Respiratorio')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los Análisis Respiratorio finalizo correctamente, pero algunas filas contenian errores). Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('biologicalMonitoring/respiratoryAnalysis')
                        ->event('Job: RespiratoryAnalysisImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de los Análisis Respiratorio')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de los Análisis Respiratorio. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('biologicalMonitoring/respiratoryAnalysis')
                    ->event('Job: RespiratoryAnalysisImportJob')
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
            $saltos = 1;
            $data['regional_employee_id'] = $row[0];
        }
        if ($confLocation['headquarter'] == 'SI')
        {
            $saltos = 2;
            $data['sede'] = $row[1];
        }
        if ($confLocation['process'] == 'SI')
        {
            $saltos = 3;
            $data['proceso'] = $row[2];

        }
        if ($confLocation['area'] == 'SI')
        {
            $saltos = 4;
            $data['area_employee_id'] = $row[3];
        }

        $data = array_merge($data,[
            'company_id' => $this->company_id,
            'patient_identification' => $row[0 + $saltos],
            'name' => $row[1 + $saltos],                                              
            'sex' => $row[2 + $saltos],
            'deal' => $row[3 + $saltos],
            'regional' => $row[4 + $saltos],
            'date_of_birth' => $this->validateDate($row[5 + $saltos]),
            'age' => is_numeric($row[6 + $saltos]) ? $row[6 + $saltos] : NULL,
            'income_date' => $this->validateDate($row[7 + $saltos]),
            'antiquity' => is_numeric($row[8 + $saltos]) ? $row[8 + $saltos] : NULL,
            'area' => $row[9 + $saltos],
            'position' => $row[10 + $saltos],
            'habits' => $row[11 + $saltos],
            'history_of_respiratory_pathologies' => $row[12 + $saltos],
            'measurement_date' => $row[13 + $saltos],
            'mg_m3_concentration' => $row[14 + $saltos],
            'ir' => $row[15 + $saltos],
            'type_of_exam' => $row[16 + $saltos],
            'year_of_spirometry' => $row[17 + $saltos],
            'spirometry' => $row[18 + $saltos],
            'date_of_realization' => $this->validateDate($row[19 + $saltos]),
            'symptomatology' => $row[20 + $saltos],
            'cvf_average_percentage' => $row[21 + $saltos],
            'vef1_average_percentage' => $row[22 + $saltos],
            'vef1_cvf_average' => $row[23 + $saltos],
            'fef_25_75_porcentage' => $row[24 + $saltos],
            'interpretation' => $row[25 + $saltos],
            'type_of_exam_2' => $row[26 + $saltos],
            'date_of_realization_2' => $this->validateDate($row[27 + $saltos]),
            'rx_oit' => $row[28 + $saltos],
            'quality' => $row[29 + $saltos],
            'yes_1' => $row[30 + $saltos],
            'not_1' => $row[31 + $saltos],
            'answer_yes_describe' => $row[32 + $saltos],
            'yes_2' => $row[33 + $saltos],
            'not_2' => $row[34 + $saltos],
            'answer_yes_describe_2' => $row[35 + $saltos],
            'other_abnormalities' => $row[36 + $saltos],
            'fully_negative' => $row[37 + $saltos],
            'observation' => $row[38 + $saltos],
            'breathing_problems' => $row[39 + $saltos],
            'classification_according_to_ats' => $row[40 + $saltos],
            'ats_obstruction_classification' => $row[41 + $saltos],
            'ats_restrictive_classification' => $row[42 + $saltos],
            'state' => $row[43 + $saltos]
        ]);

        $rules = [];

        if ($confLocation['regional'] == 'SI')
            $rules['regional_employee_id'] = 'required';
        if ($confLocation['headquarter'] == 'SI')
            $rules['sede'] = 'required';
        if ($confLocation['process'] == 'SI')
            $rules['proceso'] = 'required';
        if ($confLocation['area'] == 'SI')
            $rules['area_employee_id'] = 'required';

        $validator = Validator::make($data, $rules, 
        [
            'regional_employee_id.required' => 'El campo '.$this->keywords['regional'].' es obligatorio.',
            'sede.required' => 'El campo '.$this->keywords['headquarter'].' es obligatorio.',
            'proceso.required' => 'El campo '.$this->keywords['process'].' es obligatorio.',
            'area_employee_id.required' => 'El campo '.$this->keywords['area'].' es obligatorio.'

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
            $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional_employee_id']) : null;
            $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
            $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso']) : null; 
            $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area_employee_id']) : null;

            $record = new RespiratoryAnalysis;
            $record->company_id = $data['company_id'];            $record->employee_regional_id = $regional_id;
            $record->employee_headquarter_id = $headquarter_id;
            $record->employee_area_id = $area_id;
            $record->employee_process_id = $process_id;
            $record->patient_identification = $data['patient_identification'];
            $record->name = $data['name'];
            $record->sex = $data['sex'];
            $record->deal = $data['deal'];
            $record->regional = $data['regional'];
            $record->date_of_birth = $data['date_of_birth'];
            $record->age = $data['age'];
            $record->income_date = $data['income_date'];
            $record->antiquity = $data['antiquity'];
            $record->area = $data['area'];
            $record->position = $data['position'];
            $record->habits = $data['habits'];
            $record->history_of_respiratory_pathologies = $data['history_of_respiratory_pathologies'];
            $record->measurement_date = $data['measurement_date'];
            $record->mg_m3_concentration = $data['mg_m3_concentration'];
            $record->ir = $data['ir'];
            $record->type_of_exam = $data['type_of_exam'];
            $record->year_of_spirometry = $data['year_of_spirometry'];
            $record->spirometry = $data['spirometry'];
            $record->date_of_realization = $data['date_of_realization'];
            $record->symptomatology = $data['symptomatology'];
            $record->cvf_average_percentage = $data['cvf_average_percentage'];
            $record->vef1_average_percentage = $data['vef1_average_percentage'];
            $record->vef1_cvf_average = $data['vef1_cvf_average'];
            $record->fef_25_75_porcentage = $data['fef_25_75_porcentage'];
            $record->interpretation = $data['interpretation'];
            $record->type_of_exam_2 = $data['type_of_exam_2'];
            $record->date_of_realization_2 = $data['date_of_realization_2'];
            $record->rx_oit = $data['rx_oit'];
            $record->quality = $data['quality'];
            $record->yes_1 = $data['yes_1'];
            $record->not_1 = $data['not_1'];
            $record->answer_yes_describe = $data['answer_yes_describe'];
            $record->yes_2 = $data['yes_2'];
            $record->not_2 = $data['not_2'];
            $record->answer_yes_describe_2 = $data['answer_yes_describe_2'];
            $record->other_abnormalities = $data['other_abnormalities'];
            $record->fully_negative = $data['fully_negative'];
            $record->observation = $data['observation'];
            $record->breathing_problems = $data['breathing_problems'];
            $record->classification_according_to_ats = $data['classification_according_to_ats'];
            $record->ats_obstruction_classification = $data['ats_obstruction_classification'];
            $record->ats_restrictive_classification = $data['ats_restrictive_classification'];
            $record->state = $data['state'];
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