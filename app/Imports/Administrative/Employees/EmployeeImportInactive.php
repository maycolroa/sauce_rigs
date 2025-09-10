<?php

namespace App\Imports\Administrative\Employees;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\Administrative\Employees\Employee;
use App\Facades\Configuration;
use App\Exports\Administrative\Employees\EmployeeImportInactiveErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Exception;

class EmployeeImportInactive implements ToCollection
{
    private $company_id;
    private $user;
    private $sheet = 1;
    private $key_row = 2;
    private $errors = [];
    private $errors_data = [];

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
                            $this->checkEmployee($row);
                        else
                            $this->setErrorData($row);
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Inactivación de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de inactivación de todos los registros de empleados finalizo correctamente')
                        ->module('employees')
                        ->event('Job: EmployeeImportInactiveJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    \Log::info('Error en la inactivacion de empleados: '); 
                    \Log::info($this->errors); 
                    $nameExcel = 'export/1/empleados_errors_'.date("YmdHis").'.xlsx';                    
                    Excel::store(new EmployeeImportInactiveErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Inactivacion de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de inactivacion de empleados finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('employees')
                        ->event('Job: EmployeeImportInactiveJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info('Error en la inactivacion de empleados: '); 
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de empleados')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de inactivacion de empleados. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('employees')
                    ->event('Job: EmployeeImportInactiveJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkEmployee($row)
    {
        $data = [
            'identificacion' => $row[0],
            'fecha_inactivacion' => $row[1]
        ];
        \Log::info($data);

        $sql = Employee::where('identification', $data['identificacion']);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();

        if (!$employee)
        {
            $this->setError('La identificación no pertenece a ningun empleado');
            $this->setErrorData($row);

            return null;
        }

        $rules = ['identificacion' => 'required|numeric'];

        $validator = Validator::make($data, $rules, 
        [
            'identificacion.required' => 'El campo Identificación es obligatorio.'
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
            $employee->active = 'NO';
            $employee->date_inactivation = $data['fecha_inactivacion'] ?? date('Y-m-d');
            $employee->update();

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