<?php

namespace App\Imports\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use App\Models\Administrative\Users\User;
use Exception;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Cie11Code;
use App\Traits\UtilsTrait;

class ImportCie11 implements ToCollection, WithCalculatedFormulas
{
    use UtilsTrait;

    private $user;
    private $sheet = 1;
    private $errors = [];
    private $errors_data = [];

    public function __construct()
    {
        $this->user = User::find(5);
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
                            $this->checkCode($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de cie11')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros finalizo correctamente')
                        ->module('reinstatements')
                        ->event('Job: CheckImportCie11Job')
                        ->company(1)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/cie11_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
            
                    NotificationMail::
                        subject('Importación de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de elementos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->module('reinstatements')
                        ->event('Job: ElementImportJob')
                        ->company(1)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de cie11')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de elementos. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    ->module('reinstatements')
                    ->event('Job: ElementImportJob')
                    ->company(1)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkCode($row)
    {
        $data = [
            'codigo' => $row[0],
            'description' => $row[1]

        ];


        $rules = [
            'codigo' => 'required',
            'description' => 'required'     
        ];

        $validator = Validator::make($data, $rules, [
            'codigo.required' => 'El campo Código es requerido y debe ser unico'

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
            $codeCie11 = Cie11Code::query();
            $codeCie11 = $codeCie11->firstOrCreate(
                [
                    'code' => $data['codigo']
                ], 
                [
                    'code' => $data['codigo'], 
                    'description' => $data['description']
                ]
            );

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