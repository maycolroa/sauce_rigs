<?php

namespace App\Imports\Administrative\Positions;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Exports\Administrative\Positions\PositionImportErrorExcel;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use Hash;

class PositionImport implements ToCollection, WithCalculatedFormulas
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
                            $this->checkPosition($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de cargos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de los cargos finalizo correctamente')
                        ->module('positions')
                        ->event('Job: PositionImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/positions_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new PositionImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de cargos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los cargos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('positions')
                        ->event('Job: PositionImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de cargos')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de cargos. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrado')
                    //->message($e->getMessage())
                    ->module('positions')
                    ->event('Job: PositionImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkPosition($row)
    {
        $data = [
            'nombre' => $row[0],
            'elementos' => explode(",", $row[1])
        ];

        $rules = [
            'nombre' => 'required|string',
            'elementos' => 'nullable',
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
            $position = EmployeePosition::updateOrCreate([                
                'company_id' => $this->company_id,
                'name' => $data['nombre']
              ],
              [
                'company_id' => $this->company_id,
                'name' => $data['nombre']
              ]);

            if (COUNT($data['elementos']) && $data['elementos'][0])
                $position->elements()->sync($data['elementos']);

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