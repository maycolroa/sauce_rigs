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
use App\Traits\LocationFormTrait;

class EmployeeHacebImport implements ToCollection
{
    use ConfigurableFormTrait;
    use LocationFormTrait;

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
    private $keywords;

    public function __construct($company_id, $user)
    {
        
      $this->user = $user;
      $this->company_id = $company_id;
      $this->eps_data = EmployeeEPS::pluck('id', 'code');
      $this->afp_data = EmployeeAFP::pluck('id', 'code');
      $this->arl_data = EmployeeARL::pluck('id', 'code');
      $this->keywords = $this->getKeywordQueue($this->company_id);
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
                        if (COUNT($row) == 14 || COUNT($row) == 18 || COUNT($row) == 15)
                        {
                            if (isset($row[0]) && $row[0])
                            {
                                $this->checkEmployee($row);
                            }
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
                    \Log::info($this->errors); 
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
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de empleados')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de empleados. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
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
            'fecha_ingreso' => $row[2],
            'regional' => $row[3],
            'sede' => $row[4],
            'proceso' => $row[5],
            'area' => $row[6],
            'cargo' => $row[7],
        ];

        $data['fecha_ingreso'] = $this->validateDate($data['fecha_ingreso']);

        $sql = Employee::where('identification', $data['identificacion']);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();

        $rules = [];
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        if ($confLocation['regional'] == 'SI')
            $rules['regional'] = 'required';
        if ($confLocation['headquarter'] == 'SI')
            $rules['sede'] = 'required';
        if ($confLocation['process'] == 'SI')
            $rules['proceso'] = 'required';
        if ($confLocation['area'] == 'SI')
            $rules['area'] = 'required';

        if ($this->formModel == 'haceb')
        {
            $rules = array_merge($rules,
            [
                'identificacion' => 'required',
                'nombre' => 'required',
                'fecha_ingreso' => 'required|date',
                'cargo' => 'required'
            ]);
        }

        $validator = Validator::make($data, $rules, 
        [
            'regional.required' => 'El campo '.$this->keywords['regional'].' es obligatorio.',
            'sede.required' => 'El campo '.$this->keywords['headquarter'].' es obligatorio.',
            'proceso.required' => 'El campo '.$this->keywords['process'].' es obligatorio.',
            'area.required' => 'El campo '.$this->keywords['area'].' es obligatorio.',
            'cargo.required' => 'El campo '.$this->keywords['position'].' es obligatorio.'
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
            $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional']) : null;
            $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
            $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso']) : null;
            $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

            $data_save = [
                'identification' => $data['identificacion'],
                'name' => $data['nombre'],
                'sex' => 'Sin Sexo',
                'employee_position_id' => $this->checkPosition($data['cargo']),
                'employee_regional_id' => $regional_id,
                'employee_headquarter_id' => $headquarter_id,
                'employee_area_id' => $area_id,
                'employee_process_id' => $process_id,
                'income_date' => $data['fecha_ingreso'],
                'company_id' => $this->company_id,
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