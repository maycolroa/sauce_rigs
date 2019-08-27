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
                    if ($key > 1) //Saltar cabeceras
                    {
                        if (COUNT($row) == 116 || COUNT($row) == 117)
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
                //\Log::info($e->getMessage());
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
        $fecha = $this->validateDate($row[6]);
        $fecha_fin = $this->validateDate($row[104]);

        MusculoskeletalAnalysis::create([
            'company_id' => $this->company_id,
            'document_type' => $row[0],
            'patient_identification' => $row[1],
            'name' => $row[2],
            'professional_identification' => $row[3],
            'professional' => $row[4],
            'order' => $row[5],
            'date' => $fecha,
            'attention_code' => $row[7],
            'attention' => $row[78],
            'evaluation_type' => $row[9],
            'evaluation_format' => $row[10],
            'department' => $row[11],
            'nit_company' => $row[12],
            'company' => $row[13],
            'nit_company_mission' => $row[14],
            'company_mission' => $row[15],
            'branch_office' => $row[16],
            'sex' => $row[17],
            'age' => $row[18],
            'etareo_group' => $row[19],
            'phone' => $row[20],
            'phone_alternative' => $row[21],
            'eps' => $row[22],
            'afp' => $row[23],
            'stratum' => $row[24],
            'number_people_charge' => $row[25],
            'scholarship' => $row[26],
            'marital_status' => $row[27],
            'position' => $row[28],
            'antiquity' => $row[29],
            'ant_atep_ep' => $row[30],
            'which_ant_atep_ep' => $row[31],
            'exercise_habit' => $row[32],
            'exercise_frequency' => $row[33],
            'liquor_habit' => $row[34],
            'liquor_frequency' => $row[35],
            'exbebedor_habit' => $row[36],
            'liquor_suspension_time' => $row[37],
            'cigarette_habit' => $row[38],
            'cigarette_frequency' => $row[39],
            'habit_extra_smoker' => $row[40],
            'cigarrillo_suspension_time' => $row[41],
            'activity_extra_labor' => $row[42],
            'pressure_systolic' => $row[43],
            'pressure_diastolic' => $row[44],
            'weight' => $row[45],
            'size' => $row[46],
            'imc' => $row[47],
            'imc_lassification' => $row[48],
            'abdominal_perimeter' => $row[49],
            'abdominal_perimeter_classification' => $row[50],
            'diagnostic_code_1' => $row[51],
            'diagnostic_1' => $row[52],
            'diagnostic_code_2' => $row[53],
            'diagnostic_2' => $row[54],
            'diagnostic_code_3' => $row[55],
            'diagnostic_3' => $row[56],
            'diagnostic_code_4' => $row[57],
            'diagnostic_4' => $row[58],
            'diagnostic_code_5' => $row[59],
            'diagnostic_5' => $row[60],
            'diagnostic_code_6' => $row[61],
            'diagnostic_6' => $row[62],
            'diagnostic_code_7' => $row[63],
            'diagnostic_7' => $row[64],
            'diagnostic_code_8' => $row[65],
            'diagnostic_8' => $row[66],
            'diagnostic_code_9' => $row[67],
            'diagnostic_9' => $row[68],
            'diagnostic_code_10' => $row[69],
            'diagnostic_10' => $row[70],
            'diagnostic_code_11' => $row[71],
            'diagnostic_11' => $row[72],
            'diagnostic_code_12' => $row[73],
            'diagnostic_12' => $row[74],
            'diagnostic_code_13' => $row[75],
            'diagnostic_13' => $row[76],
            'diagnostic_code_14' => $row[77],
            'diagnostic_14' => $row[78],
            'diagnostic_code_15' => $row[79],
            'diagnostic_15' => $row[80],
            'diagnostic_code_16' => $row[81],
            'diagnostic_16' => $row[82],
            'diagnostic_code_17' => $row[83],
            'diagnostic_17' => $row[84],
            'diagnostic_code_18' => $row[85],
            'diagnostic_18' => $row[86],
            'cardiovascular_risk' => $row[87],
            'osteomuscular_classification' => $row[88],
            'osteomuscular_group' => $row[89],
            'age_risk' => $row[90],
            'pathological_background_risks' => $row[91],
            'extra_labor_activities_risk' => $row[92],
            'sedentary_risk' => $row[93],
            'imc_risk' => $row[94],
            'consolidated_personal_risk_punctuation' => $row[95],
            'consolidated_personal_risk_criterion' => $row[96],
            'prioritization_medical_criteria' => $row[97],
            'concept' => $row[98],
            'recommendations' => $row[99],
            'observations' => $row[100],
            'restrictions' => $row[101],
            'remission' => $row[102],
            'authorization_access_information' => $row[103],
            'date_end' => $fecha_fin,
            'description_medical_exam' => $row[105],
            'symptom' => $row[106],
            'symptom_type' => $row[107],
            'body_part' => $row[108],
            'periodicity' => $row[109],
            'workday' => $row[110],
            'Symptomatology_observations' => $row[111],
            'optometry' => $row[112],
            'visiometry' => $row[113],
            'audiometry' => $row[114],
            'spirometry' => $row[115],
            'tracing' => $row[116]
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