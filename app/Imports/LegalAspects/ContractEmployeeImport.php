<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use App\Models\Administrative\Roles\Role;
use App\Models\LegalAspects\Contracts\CompanyLimitCreated;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\HighRiskType;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Models\General\Team;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Models\General\Company;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsEmployeeImportErrorExcel;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use Hash;

class ContractEmployeeImport implements ToCollection, WithCalculatedFormulas
{
    use ContractTrait;
    use UserTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    protected $contract;

    public function __construct($company_id, $user, $contract)
    {        
      $this->user = $user;
      $this->company_id = $company_id;
      $this->contract = $contract;
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
                            $this->checkContractEmployee($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de los empleados finalizo correctamente')
                        ->module('contracts')
                        ->event('Job: ContractEmployeeImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/contracts_employee_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new ContractsEmployeeImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de contratistas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los contratistas finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('contracts')
                        ->event('Job: ContractEmployeeImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de contratistas')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de contratistas. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('contracts')
                    ->event('Job: ContractEmployeeImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkContractEmployee($row)
    {
        $data = [
            'nombre_empleado' => $row[0],
            'documento_empleado' => $row[1],
            'email_empleado' => $row[2],
            'posicion' => ucfirst($row[3]),
            'actividades' => explode(",", $row[4])
        ];

        $rules = [
            'nombre_empleado' => 'required|string',
            'documento_empleado' => 'required',
            'email_empleado' => 'required|email',
            'posicion' => 'required|string',
            'actividades' => 'required'    
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
                //////////////////////////Creacion empleado/////////////////

                    $employee = new ContractEmployee();
                    $employee->name = $data['nombre_empleado'];
                    $employee->email = $data['email_empleado'];
                    $employee->identification = $data['documento_empleado'];
                    $employee->position = $data['posicion'];
                    $employee->company_id = $this->company_id;
                    $employee->contract_id = $this->contract->id;
                    $employee->token = Hash::make($data['email_empleado'].$data['documento_empleado']);
                    $employee->save();

                    $employee->activities()->sync($data['actividades']);

                    TrainingSendNotificationJob::dispatch($this->company_id, '', $employee->id);


                ////////////////Envio de capacitacione///////////////



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