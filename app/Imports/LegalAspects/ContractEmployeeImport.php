<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use App\Models\Administrative\Roles\Role;
use App\Models\LegalAspects\Contracts\CompanyLimitCreated;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\HighRiskType;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Models\Administrative\Employees\EmployeeAFP;
use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\General\Departament;
use App\Models\General\Municipality;
use App\Models\General\Team;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use App\Traits\UtilsTrait;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Models\General\Company;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeeImportErrorExcel;
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
    use UtilsTrait;

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
      $this->afp_data = EmployeeAFP::pluck('id', 'code');
      $this->eps_data = EmployeeEPS::pluck('id', 'code');
      $this->departament_data = Departament::pluck('id', 'id');
      $this->municipality_data = Municipality::pluck('id', 'id');
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
                    Excel::store(new ContractsEmployeeImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id, $this->contract->id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
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
            'fecha_nacimiento'=> $row[2],
            'sexo' => $row[3],
            'tel_residencia' => $row[4],
            'tel_movil' => $row[5],
            'civil_status' => $row[6],
            'direccion' => $row[7],
            'email_empleado' => $row[8],
            'working_day' => $row[9],
            'departament' => $row[10],
            'municipality' => $row[11],
            'income_date' => $row[12],
            'posicion' => ucfirst($row[13]),
            'discapacidad' => strtoupper($row[14]),
            'descrip_discapacidad' => $row[15],
            'contacto_emergencia' => $row[16],
            'tel_contacto_emergencia' => $row[17],
            'rh' => $row[18],
            'salario' => $row[19],
            'afp' => $row[20],
            'eps' => $row[21],
            'actividades' => explode(",", $row[22]),
            'proyectos' => isset($row[23]) && $row[23] ? explode(",", $row[23]) : []
        ];

        $data['fecha_nacimiento'] = $this->validateDate($data['fecha_nacimiento']);
        $data['income_date'] = $this->validateDate($data['income_date']);

        $data['afp'] = $data['afp'] ? (isset($this->afp_data[$data['afp']]) ? $this->afp_data[$data['afp']] : -1) : null;

        $data['eps'] = $data['eps'] ? (isset($this->eps_data[$data['eps']]) ? $this->eps_data[$data['eps']] : -1) : null;

        $data['departament'] = $data['departament'] ? (isset($this->departament_data[$data['departament']]) ? $this->departament_data[$data['departament']] : -1) : null;

        $data['municipality'] = $data['municipality'] ? (isset($this->municipality_data[$data['municipality']]) ? $this->municipality_data[$data['municipality']] : -1) : null;

        $municipio = Municipality::where('id', $data['municipality'])->where('departament_id', $data['departament'])->first();

        if (!$municipio)
        {
            $this->setError('El municipio ingresado no corresponde al departamento ingresado');
            $this->setErrorData($row);

            return null;
        }

        $id = NULL;

        $employee_exist = ContractEmployee::where('identification', $data['documento_empleado'])->where('company_id', $this->company_id)->where('contract_id', $this->contract->id)->first();

        if ($employee_exist)
        {
            $this->setError('La identificacion del empleado ya existe');
            $this->setErrorData($row);

            return null;
        }

        $rules = [
            'nombre_empleado' => 'required|string',
            'documento_empleado' => 'required',
            'email_empleado' => 'required|email',
            'posicion' => 'required|string',
            'actividades' => 'required',
            'afp' => 'required|exists:sau_employees_afp,id',
            'eps' => 'required|exists:sau_employees_eps,id',
            'fecha_nacimiento'=> 'required|date',
            'sexo' => 'required',
            'tel_residencia' => 'nullable',
            'tel_movil' => 'required',
            'direccion' => 'required',
            'discapacidad' => 'required',
            'descrip_discapacidad' => 'nullable',
            'contacto_emergencia' => 'required',
            'tel_contacto_emergencia' => 'required',
            'rh' => 'required',
            'salario' => 'required',
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

                $email_valid = $this->verifyEmailFormat($data['email_empleado']);

                if ($email_valid)
                {
                    $tok = Hash::make($data['email_empleado'].$data['documento_empleado']);
                    $tok = str_replace("/", "a", $tok);

                    $employee = new ContractEmployee();
                    $employee->name = $data['nombre_empleado'];
                    $employee->email = $data['email_empleado'];
                    $employee->identification = $data['documento_empleado'];
                    $employee->position = $data['posicion'];
                    $employee->company_id = $this->company_id;
                    $employee->contract_id = $this->contract->id;
                    $employee->token = $tok;
                    $employee->employee_afp_id = $data['afp'];
                    $employee->employee_eps_id = $data['eps'];
                    $employee->sex = $data['sexo'];
                    $employee->phone_residence = $data['tel_residencia'];
                    $employee->phone_movil = $data['tel_movil'];
                    $employee->direction = $data['direccion'];
                    $employee->disability_condition = $data['discapacidad'];
                    $employee->emergency_contact = $data['contacto_emergencia'];
                    $employee->rh = $data['rh'];
                    $employee->salary = $data['salario'];
                    $employee->date_of_birth = $data['fecha_nacimiento'];
                    $employee->disability_description = $data['descrip_discapacidad'];
                    $employee->emergency_contact_phone = $data['tel_contacto_emergencia'];
                    $employee->workday = $data['working_day'];
                    $employee->income_date = $data['income_date'];
                    $employee->departament_id = $data['departament'];
                    $employee->city_id = $data['municipality'];
                    $employee->civil_status = $data['civil_status'];
                    $employee->save();

                    $employee->activities()->sync($data['actividades']);
                    $employee->proyects()->sync($data['proyectos']);

                    TrainingSendNotificationJob::dispatch($this->company_id, '', $employee->id);
                }
                else
                {
                    $this->setError('Formato de email incorrecto');
                    $this->setErrorData($row);

                    return null;
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

    private function validateDate($date)
    {
        try
        {
            $d = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        }
        catch (\Exception $e) {
            return $date;
        }

        return $d ? $d : null;
    }
}