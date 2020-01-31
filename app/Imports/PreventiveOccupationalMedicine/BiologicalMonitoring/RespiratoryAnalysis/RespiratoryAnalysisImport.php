<?php

namespace App\Imports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysis;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Validator;
use Exception;

class RespiratoryAnalysisImport implements ToCollection
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
                    if ($key > 0) //Saltar cabeceras
                    {
                        if (COUNT($row) == 44 || COUNT($row) == 45)
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
                    /*$nameExcel = 'export/1/analisis_Respiratorio_errors_'.date("YmdHis").'.xlsx';
                    Excel::store(new AudiometryImportErrorExcel(collect($this->errors_data), $this->errors), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);*/
            
                    NotificationMail::
                        subject('Importación de los Análisis Respiratorio')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los Análisis Respiratorio finalizo correctamente, pero algunas filas contenian errores')//. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        //->subcopy('Este link es valido por 24 horas')
                        //->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
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
                    ->message('Se produjo un error durante el proceso de importación de los Análisis Respiratorio. Contacte con el administrador')
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
        //$fecha = $this->validateDate($row[6]);

        RespiratoryAnalysis::create([
            'company_id' => $this->company_id,
            'patient_identification' => $row[0],
            'name' => $row[1],
            //'date' => date('Y-m-d'),
            'sex' => $row[2],
            'deal' => $row[3],
            'regional' => $row[4],
            'date_of_birth' => $this->validateDate($row[5]),
            'age' => is_numeric($row[6]) ? $row[6] : NULL,
            'income_date' => $this->validateDate($row[7]),
            'antiquity' => is_numeric($row[8]) ? $row[8] : NULL,
            'area' => $row[9],
            'position' => $row[10],
            'habits' => $row[11],
            'history_of_respiratory_pathologies' => $row[12],
            'measurement_date' => $row[13],
            'mg_m3_concentration' => $row[14],
            'ir' => $row[15],
            'type_of_exam' => $row[16],
            'year_of_spirometry' => $row[17],
            'spirometry' => $row[18],
            'date_of_realization' => $this->validateDate($row[19]),
            'symptomatology' => $row[20],
            'cvf_average_percentage' => $row[21],
            'vef1_average_percentage' => $row[22],
            'vef1_cvf_average' => $row[23],
            'fef_25_75_porcentage' => $row[24],
            'interpretation' => $row[25],
            'type_of_exam_2' => $row[26],
            'date_of_realization_2' => $this->validateDate($row[27]),
            'rx_oit' => $row[28],
            'quality' => $row[29],
            'yes_1' => $row[30],
            'not_1' => $row[31],
            'answer_yes_describe' => $row[32],
            'yes_2' => $row[33],
            'not_2' => $row[34],
            'answer_yes_describe_2' => $row[35],
            'other_abnormalities' => $row[36],
            'fully_negative' => $row[37],
            'observation' => $row[38],
            'breathing_problems' => $row[39],
            'classification_according_to_ats' => $row[40],
            'ats_obstruction_classification' => $row[41],
            'ats_restrictive_classification' => $row[42],
            'state' => $row[43]
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