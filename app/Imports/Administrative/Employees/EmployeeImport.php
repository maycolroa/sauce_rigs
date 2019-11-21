<?php

namespace App\Imports\Administrative\Employees;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Employees\EmployeeEPS;
use App\Models\Administrative\Employees\EmployeeAFP;
use App\Models\Administrative\Employees\EmployeeARL;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Business\EmployeeBusiness;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Facades\Configuration;
use App\Exports\Administrative\Employees\EmployeeImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Exception;
use App\Traits\ConfigurableFormTrait;

class EmployeeImport implements ToCollection
{
    use ConfigurableFormTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $formModel;
    private $eps_data = [];
    private $afp_data = [];
    private $arl_data = [];
    private $contract_types = [];

    public function __construct($company_id, $user)
    {
        
      $this->user = $user;
      $this->company_id = $company_id;
      $this->eps_data = EmployeeEPS::pluck('id', 'code');
      $this->afp_data = EmployeeAFP::pluck('id', 'code');
      $this->arl_data = EmployeeARL::pluck('id', 'code');
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {
            
            try
            {
                $employees_id = [];

                $this->formModel = $this->getFormModel('form_employee', $this->company_id);

                if ($this->formModel == 'misionEmpresarial')
                    $this->contract_types = implode(",", $this->getSelectOptions('employee_select_contract_types', false, $this->company_id));

                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (COUNT($row) == 14 || COUNT($row) == 18)
                        {
                            $this->checkEmployee($row);
                        }
                        else
                        {
                            $this->setError('Formato inválido');
                            $this->setErrorData($row);
                           
                        }
                    }
                }

                

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de empleados finalizo correctamente')
                        ->module('employees')
                        ->event('Job: EmpployeeImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/empleados_errors_'.date("YmdHis").'.xlsx';                    
                    Excel::store(new EmployeeImportErrorExcel(collect($this->errors_data), $this->errors, $this->formModel, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de empleados')
                        ->recipients($this->user)
                        ->message('El proceso de importación de empleados finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('employees')
                        ->event('Job: EmpployeeImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                NotificationMail::
                    subject('Importación de empleados')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de empleados. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('employees')
                    ->event('Job: EmpployeeImportJob')
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
            'nombre' => $row[1],
            'fecha_nacimiento' => $row[2],
            'sexo' => $row[3],
            'email' => $row[4],
            'fecha_ingreso' => $row[5],
            'regional' => $row[6],
            'sede' => $row[7],
            'proceso' => $row[8],
            'area' => $row[9],
            'cargo' => $row[10],
            'centro_costo' => $row[11],
            'negocio' => ($this->formModel == 'default') ? $row[12] : null,
            'eps' => ($this->formModel == 'default') ? (string) $row[13] : ( ($this->formModel == 'vivaAir' || $this->formModel == 'misionEmpresarial') ? (string) $row[12] : null),
            'afp' => ($this->formModel == 'vivaAir' || $this->formModel == 'misionEmpresarial') ? (string) $row[13] : null,
            'arl' => ($this->formModel == 'misionEmpresarial') ? (string) $row[14] : null,
            'numero_contrato' => ($this->formModel == 'misionEmpresarial') ? $row[15] : null,
            'fecha_ultimo_contrato' => ($this->formModel == 'misionEmpresarial') ? $this->validateDate($row[16]) : null,
            'tipo_contrato' => ($this->formModel == 'misionEmpresarial') ? $row[17] : null,
        ];

        $data['fecha_nacimiento'] = $this->validateDate($data['fecha_nacimiento']);
        $data['fecha_ingreso'] = $this->validateDate($data['fecha_ingreso']);
        $data['eps'] = $data['eps'] ? (isset($this->eps_data[$data['eps']]) ? $this->eps_data[$data['eps']] : -1) : null;
        $data['afp'] = $data['afp'] ? (isset($this->afp_data[$data['afp']]) ? $this->afp_data[$data['afp']] : -1) : null;
        $data['arl'] = $data['arl'] ? (isset($this->arl_data[$data['arl']]) ? $this->arl_data[$data['arl']] : -1) : null;

        $sql = Employee::where('identification', $data['identificacion']);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();


        if ($this->formModel == 'default' || $this->formModel == 'vivaAir')
        {
            $rules = [
                'identificacion' => 'required|numeric',
                'nombre' => 'required|string',
                'fecha_nacimiento' => 'nullable|date',
                'sexo' => 'required|string|in:Masculino,Femenino,Sin Sexo',
                'email' => 'nullable|email|unique:sau_employees,email,'.($employee ? $employee->id : null).',id,company_id,'.$this->company_id,
                'fecha_ingreso' => 'required|date',
                'regional' => 'required',
                'sede' => 'required',
                'proceso' => 'required',
                'area' => 'nullable',
                'cargo' => 'required',
                'centro_costo' => 'nullable',
                'eps' => 'nullable|exists:sau_employees_eps,id',
                'afp' => 'nullable|exists:sau_employees_afp,id'
            ];
        }
        else if ($this->formModel == 'misionEmpresarial')
        {
            $rules = [
                'identificacion' => 'required|numeric',
                'nombre' => 'required|string',
                'fecha_nacimiento' => 'nullable|date',
                'sexo' => 'required|string|in:Masculino,Femenino,Sin Sexo',
                'email' => 'nullable|email|unique:sau_employees,email,'.($employee ? $employee->id : null).',id,company_id,'.$this->company_id,
                'fecha_ingreso' => 'required|date',
                'regional' => 'required',
                'sede' => 'required',
                'proceso' => 'required',
                'area' => 'nullable',
                'cargo' => 'required',
                'centro_costo' => 'nullable',
                'eps' => 'nullable|exists:sau_employees_eps,id',
                'afp' => 'nullable|exists:sau_employees_afp,id',
                'arl' => 'nullable|exists:sau_employees_arl,id',
                'numero_contrato' => 'required|integer|min:0',
                'tipo_contrato' => 'required|in:'.$this->contract_types
            ];
        }

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
            $regional_id = $this->checkRegional($data['regional']);
            $headquarter_id = $this->checkHeadquarter($regional_id, $data['sede']);
            $process_id = $this->checkProcess($headquarter_id, $data['proceso']);
            $area_id = $data['area'] ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

            $data_save = [
                'identification' => $data['identificacion'],
                'name' => $data['nombre'],
                'sex' => $data['sexo'],
                'email' => $data['email'],
                'date_of_birth' => $data['fecha_nacimiento'],
                'employee_position_id' => $this->checkPosition($data['cargo']),
                'employee_business_id' => $this->checkBusiness($data['centro_costo']),
                'employee_regional_id' => $regional_id,
                'employee_headquarter_id' => $headquarter_id,
                'employee_area_id' => $area_id,
                'employee_process_id' => $process_id,
                'employee_eps_id' => $data['eps'],
                'income_date' => $data['fecha_ingreso'],
                'company_id' => $this->company_id,
                'deal' => $data['negocio'],
                'employee_afp_id' => $data['afp'],
                'employee_arl_id' => $data['arl'],
                'contract_numbers' => $data['numero_contrato'],
                'last_contract_date' => $data['fecha_ultimo_contrato'],
                'contract_type' => $data['tipo_contrato']
            ];

            if ($employee)
                $employee->update($data_save);
            else 
                Employee::create($data_save);

            return true;
        }
    }

    private function checkArea($headquarter_id, $process_id, $area_name)
    {        
        $area = EmployeeRegional::select("sau_employees_areas.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->join('sau_process_area', 'sau_process_area.employee_process_id', 'sau_employees_processes.id')
            ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_process_area.employee_area_id')
            ->where('sau_employees_areas.name', $area_name)
            ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name');

        $area->company_scope = $this->company_id;
        $area = $area->first();

        if (!$area)
        {
            $area = new EmployeeArea();
            $area->name = $area_name;
            $area->save();
        }
        else
            $area = EmployeeArea::find($area->id);
        
        $area->processes()->wherePivot('employee_headquarter_id','=',$headquarter_id)->detach($process_id);
        $area->processes()->attach($process_id, ['employee_headquarter_id' => $headquarter_id]);

        return $area->id;
    }

    private function checkRegional($name)
    {
        $regional = EmployeeRegional::query();
        $regional->company_scope = $this->company_id;
        $regional = $regional->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $regional->id;
    }

    private function checkPosition($name)
    {
        $position = EmployeePosition::query();
        $position->company_scope = $this->company_id;
        $position = $position->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $position->id;
    }

    private function checkBusiness($name)
    {
        if ($name)
        {
            $business = EmployeeBusiness::query();
            $business->company_scope = $this->company_id;
            $business = $business->firstOrCreate(['name' => $name], 
                                                ['name' => $name, 'company_id' => $this->company_id]);

            return $business->id;
        }

        return NULL;
    }

    private function checkHeadquarter($regional_id, $headquarter)
    {
        $headquarter = EmployeeHeadquarter::firstOrCreate(['name' => $headquarter, 'employee_regional_id' => $regional_id], 
                                            ['name' => $headquarter, 'employee_regional_id' => $regional_id]);

        return $headquarter->id;
    }

    private function checkProcess($headquarter_id, $process_name)
    {
        $process = EmployeeRegional::select("sau_employees_processes.id as id")
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
            ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
            ->where('sau_employees_processes.name', $process_name)
            ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name');

        $process->company_scope = $this->company_id;
        $process = $process->first();

        if (!$process)
        {
            $process = new EmployeeProcess();
            $process->name = $process_name;
            $process->save();
        }
        else
            $process = EmployeeProcess::find($process->id);
        
        $process->headquarters()->detach($headquarter_id);
        $process->headquarters()->attach($headquarter_id);

        return $process->id;
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