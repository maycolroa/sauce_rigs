<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Facades\Configuration;
use App\Exports\Administrative\Employees\EmployeeImportInactiveErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Exception;

class ContractEmployeeImportSocialSecure implements ToCollection
{
    private $company_id;
    private $user;
    private $sheet = 1;
    private $key_row = 2;
    private $errors = [];
    private $errors_data = [];
    private $file_social_secure;

    public function __construct($company_id, $user, $contract, $file_social_secure)
    {        
      $this->user = $user;
      $this->contract = $contract;
      $this->company_id = $company_id;
      $this->file_social_secure = $file_social_secure;
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
                        subject('Carga de Seguridad Social de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de carga de Seguridad Social de todos los registros de empleados finalizo correctamente')
                        ->module('employees')
                        ->event('Job: ContractEmployeeImportSocialSecureJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    \Log::info('Error en la carga de Seguridad Social de empleados: '); 
                    \Log::info($this->errors); 
                    $nameExcel = 'export/1/empleados_errors_'.date("YmdHis").'.xlsx';                    
                    Excel::store(new EmployeeImportInactiveErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Carga de Seguridad Social de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de carga de la seguridad social de empleados finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('employees')
                        ->event('Job: ContractEmployeeImportSocialSecureJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info('Error en la carga de Seguridad Social de empleados: '); 
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Carga de Seguridad Social de empleados')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de carga de Seguridad Social de empleados. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('employees')
                    ->event('Job: ContractEmployeeImportSocialSecureJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkEmployee($row)
    {
        $data = [
            'identificacion' => $row[0],
        ];

        $sql = ContractEmployee::where('identification', $data['identificacion'])->where('contract_id', $this->contract->id);
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
            $fileUpload = new FileUpload();
            $fileUpload->user_id = $this->user->id;
            $fileUpload->name = 'Seguridad Social';
            $fileUpload->file = $this->file_social_secure ;
            $fileUpload->expirationDate = NULL;
            $fileUpload->apply_file = 'SI';
            $fileUpload->apply_motive = NULL;
            $fileUpload->save();

            $fileUpload->contracts()->sync([$this->contract->id]);
            $class_document = [];

            foreach ($employee->activities as $key => $activity) 
            {          
              $class_document = array_merge($class_document, $activity->documents->where('class', 'Seguridad social')->pluck('id')->toArray());
            }

            $ids = [];

            foreach ($class_document as $value) 
            {               
                $ids[$value] = ['employee_id' => $employee->id];
            }

            $fileUpload->documents()->sync($ids);

            $state = new FileModuleState;
            $state->contract_id = $this->contract->id;
            $state->file_id = $fileUpload->id;
            $state->module = 'Empleados';
            $state->state = 'CREADO';                            
            $state->date = date('Y-m-d');
            $state->save();

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