<?php

namespace App\Imports\IndustrialSecure\Epp;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\IndustrialSecure\Epp\Location;
use App\Exports\IndustrialSecure\Epp\LocationImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use App\Traits\ConfigurableFormTrait;
use App\Traits\LocationFormTrait;

class LocationImport implements ToCollection, WithCalculatedFormulas
{
    use ConfigurableFormTrait;
    use LocationFormTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $keywords;

    public function __construct($company_id, $user)
    {
        
      $this->user = $user;
      $this->company_id = $company_id;
      $this->keywords = $this->getKeywordQueue($this->company_id);
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
                            $this->checkLocation($row);
                        }
                        /*else
                        {
                            $this->setError('Formato inválido');
                            $this->setErrorData($row);
                           
                        }*/
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de ubicaciones')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de la ubicaciones finalizo correctamente')
                        ->module('epp')
                        ->event('Job: DangerMatrixImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/locations_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new LocationImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de ubicacioness')
                        ->recipients($this->user)
                        ->message('El proceso de importación de ubicacioness finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('dangerMatrix')
                        ->event('Job: DangerMatrixImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de ubicacioness')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de ubicacioness. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('dangerMatrix')
                    ->event('Job: DangerMatrixImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkLocation($row)
    {
        $data = [];
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        $data['nombre'] = $row[0];

        if ($confLocation['regional'] == 'SI')
            $data['regional'] = $row[1];

        if ($confLocation['headquarter'] == 'SI')
            $data['sede'] = $row[2];

        if ($confLocation['process'] == 'SI')
            $data['proceso'] = $row[3];

        if ($confLocation['area'] == 'SI')
            $data['area'] = $row[4];


        $rules = [];

        if ($confLocation['regional'] == 'SI')
            $rules['regional'] = 'required';
        if ($confLocation['headquarter'] == 'SI')
            $rules['sede'] = 'required';
        if ($confLocation['process'] == 'SI')
            $rules['proceso'] = 'required';
        if ($confLocation['area'] == 'SI')
            $rules['area'] = 'required';
    
        $rules = array_merge($rules,
        [
            'nombre' => 'required'           
        ]);

        $validator = Validator::make($data, $rules, 
        [
            'regional.required' => 'El campo '.$this->keywords['regional'].' es obligatorio.',
            'sede.required' => 'El campo '.$this->keywords['headquarter'].' es obligatorio.',
            'proceso.required' => 'El campo '.$this->keywords['process'].' es obligatorio.',
            'area.required' => 'El campo '.$this->keywords['area'].' es obligatorio.'

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

                $location = new Location();
                $location->company_id = $this->company_id;
                $location->name = $data['nombre'];
                $location->employee_regional_id = $regional_id;
                $location->employee_headquarter_id = $headquarter_id;
                $location->employee_area_id = $area_id;
                $location->employee_process_id = $process_id;
                $location->save();

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
            $process->types = NULL;
            $process->save();
        }
        else
        {
            $process = EmployeeProcess::find($process->id);
            $process->save();
        }
        
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