<?php

namespace App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\MusculoskeletalAnalysis\MusculoskeletalAnalysis;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
use Exception;

class MusculoskeletalAnalysisImport implements ToCollection
{
    private $company_id;
    private $user;
    private $errors = [];
    private $sheet = 1;

    public function __construct($company_id, $user)
    {
      $this->user = $user;
      $this->company_id = $company_id;
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {
            try
            {
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 4) //Saltar cabeceras
                    {
                        if (COUNT($row) == 85 || COUNT($row) == 86)
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
                    /*$nameExcel = 'export/1/analisis_osteomuscular_errors_'.date("YmdHis").'.xlsx';
                    Excel::store(new AudiometryImportErrorExcel(collect($this->errors_data), $this->errors), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);*/
            
                    NotificationMail::
                        subject('Importación de los Análisis Osteomuscular')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los Análisis Osteomuscular finalizo correctamente, pero algunas filas contenian errores')//. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        //->subcopy('Este link es valido por 24 horas')
                        //->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
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
                    ->message('Se produjo un error durante el proceso de importación de los Análisis Osteomuscular. Contacte con el administrador')
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
        //$fecha = $this->validateDate($row[6]);

        MusculoskeletalAnalysis::create([
            'company_id' => $this->company_id,
            'patient_identification' => $row[1],
            'name' => $row[2],
            'date' => date('Y-m-d'),
            'evaluation_type' => $row[3],
            'evaluation_format' => $row[4],
            'company' => $row[5],
            'branch_office' => $row[6],
            'sex' => $row[7],
            'age' => $row[8],
            'phone' => $row[9],
            'phone_alternative' => $row[10],
            'eps' => $row[11],
            'afp' => $row[12],
            'position' => $row[13],
            'antiquity' => $row[14],
            'state' => $row[15],
            'ant_atep_ep' => $row[16],
            'which_ant_atep_ep' => $row[17],
            'exercise_habit' => $row[18],
            'exercise_frequency' => $row[19],
            'liquor_habit' => $row[20],
            'liquor_frequency' => $row[21],
            'exbebedor_habit' => $row[22],
            'liquor_suspension_time' => $row[23],
            'cigarette_habit' => $row[24],
            'cigarette_frequency' => $row[25],
            'habit_extra_smoker' => $row[26],
            'cigarrillo_suspension_time' => $row[27],
            'activity_extra_labor' => $row[28],
            'weight' => $row[29],
            'size' => $row[30],
            'imc' => $row[31],
            'imc_lassification' => $row[32],
            'abdominal_perimeter' => $row[33],
            'abdominal_perimeter_classification' => $row[34],
            'diagnostic_code_1' => $row[35],
            'diagnostic_1' => $row[36],
            'diagnostic_code_2' => $row[37],
            'diagnostic_2' => $row[38],
            'diagnostic_code_3' => $row[39],
            'diagnostic_3' => $row[40],
            'diagnostic_code_4' => $row[41],
            'diagnostic_4' => $row[42],
            'diagnostic_code_5' => $row[43],
            'diagnostic_5' => $row[44],
            'diagnostic_code_6' => $row[45],
            'diagnostic_6' => $row[46],
            'diagnostic_code_7' => $row[47],
            'diagnostic_7' => $row[48],
            'diagnostic_code_8' => $row[49],
            'diagnostic_8' => $row[50],
            'diagnostic_code_9' => $row[51],
            'diagnostic_9' => $row[52],
            'diagnostic_code_10' => $row[53],
            'diagnostic_10' => $row[54],
            'diagnostic_code_11' => $row[55],
            'diagnostic_11' => $row[56],
            'diagnostic_code_12' => $row[57],
            'diagnostic_12' => $row[58],
            'diagnostic_code_13' => $row[59],
            'diagnostic_13' => $row[60],
            'cardiovascular_risk' => $row[61],
            'osteomuscular_classification' => $row[62],
            'osteomuscular_group' => $row[63],
            'age_risk' => $row[64],
            'pathological_background_risks' => $row[65],
            'extra_labor_activities_risk' => $row[66],
            'sedentary_risk' => $row[67],
            'imc_risk' => $row[68],
            'consolidated_personal_risk_punctuation' => $row[69],
            'consolidated_personal_risk_criterion' => $row[70],
            'prioritization_medical_criteria' => $row[71],
            'concept' => $row[72],
            'recommendations' => $row[73],
            'observations' => $row[74],
            'restrictions' => $row[75],
            'remission' => $row[76],
            'description_medical_exam' => $row[77],
            'symptom' => $row[78],
            'symptom_type' => $row[79],
            'body_part' => $row[80],
            'periodicity' => $row[81],
            'workday' => $row[82],
            'symptomatology_observations' => $row[83],
            'id3' => $row[84]
        ]);
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
}