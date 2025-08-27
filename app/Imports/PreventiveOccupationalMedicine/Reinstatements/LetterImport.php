<?php

namespace App\Imports\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Exports\Administrative\Positions\PositionImportErrorExcel;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\LetterHistory;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use Hash;
use Carbon\Carbon;

class LetterImport implements ToCollection, WithCalculatedFormulas
{
    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;

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
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkLetter($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de cartas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de las cartas finalizo correctamente')
                        ->module('reinstatements')
                        ->event('Job: LetterImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/cartas_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);               
            
                    NotificationMail::
                        subject('Importación de cartas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los cartas finalizo correctamente, pero algunas filas contenian errores')
                        ->module('reinstatements')
                        ->event('Job: LetterImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de cartas')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de cartas. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    ->module('reinstatements')
                    ->event('Job: LetterImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkLetter($row)
    {
        $data = [
            'company_id' => $row[0],
            'check_id' => $row[1],
            'created_at' => $row[2],
            'detail' => $row[3],
            'start_recommendations' => $row[4],
            'end_recommendations' => $row[5],
            'indefinite_recommendations' => $row[6],
            'origin_recommendations' => $row[7],
            'disease_origin' => $row[8]
        ];

        $rules = [
            'company_id' => 'required',
            'check_id' => 'required',
            'created_at' => 'required',
        ];

        $validator = Validator::make($data, $rules);

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
            $unixTimestamp = ($data['created_at'] - 25569) * 86400;

            $date = Carbon::createFromTimestamp($unixTimestamp);
            $formattedDate = $date->format('Y-m-d');

            $data['start_recommendations'] = $data['start_recommendations'] ? ($data['start_recommendations'] + 1) : NULL;
            $unixTimestamp_start = $data['start_recommendations'] ? (($data['start_recommendations'] - 25569) * 86400) : NULL;

            if ($unixTimestamp_start)
            {
                $start_recommendations = Carbon::createFromTimestamp($unixTimestamp_start);
                $start_recommendations = $start_recommendations->format('Y-m-d');
            }
            else
                $start_recommendations = NULL;

            $data['end_recommendations'] = $data['end_recommendations'] ? ($data['end_recommendations'] + 1) : NULL;
            $unixTimestamp_end = $data['end_recommendations'] ? (($data['end_recommendations'] - 25569) * 86400) : NULL;

            if ($unixTimestamp_end)
            {
                $end_recommendations = Carbon::createFromTimestamp($unixTimestamp_end);
                $end_recommendations = $end_recommendations->format('Y-m-d');
            }
            else   
                $end_recommendations = NULL;

            $letters = LetterHistory::select('*')
            ->withoutGlobalScopes()
            ->where('company_id', 669)
            ->whereDate('created_at', $formattedDate)
            ->where('check_id', $data['check_id'])
            ->get();
            
            if ($letters->count() > 0)
            {
                foreach ($letters as $key => $letter) 
                {
                    \Log::info($letter);
                    $letter->update(
                        [
                            'detail' => $data['detail'],
                            'start_recommendations' => $start_recommendations,
                            'end_recommendations' => $end_recommendations,
                            'indefinite_recommendations' => $data['indefinite_recommendations'],
                            'origin_recommendations' => $data['origin_recommendations'],
                            'disease_origin' => $data['disease_origin']
                        ]
                    );
                }
            }

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
}