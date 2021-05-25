<?php

namespace App\Imports\IndustrialSecure\DangerousConditions;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use App\Traits\LocationFormTrait;
use App\Traits\UtilsTrait;
use DB;

class InspectionsImport implements ToCollection, WithCalculatedFormulas
{
    use LocationFormTrait, UtilsTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $inspection;
    private $theme_compliance;
    private $locations;
    private $sum_compliance = [];
    private $sum_parcial = [];

    public function __construct($company_id, $user, $locations)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->locations = $locations;
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
                            $this->checkInspection($row, $key);
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
                        subject('Importación de inspecciones planeadas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de la inspecciones planeadas finalizo correctamente')
                        ->module('dangerousConditions')
                        ->event('Job: InspectionImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/danger_matrix_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new InspectionImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de inspecciones planeadas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de inspecciones planeadas finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('dangerousConditions')
                        ->event('Job: InspectionImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de inspecciones planeadas')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de inspecciones planeadas. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('dangerousConditions')
                    ->event('Job: InspectionImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkInspection($row, $key)
    {
        $data = [    
            'nombre' => $row[0],
            'tipo' => ucfirst($row[1]),
            'cumplimiento_parcial_tipo_1' => $row[2],
            'tema' => $row[3],
            'item' => $row[4],
            'valor_cumplimiento_item_tipo_2' => $row[5],
            'valor_cumplimiento_parcial_item_tipo_2' => $row[6]
        ];

        $rules = [
            'nombre' => 'required',
            'tipo' => 'required|in:Tipo 1,Tipo 2',
            'cumplimiento_parcial_tipo_1' => 'nullable|numeric|max:1',
            'tema' => 'required',
            'item' => 'required',
            'valor_cumplimiento_item_tipo_2' => 'nullable|numeric|max:100',
            'valor_cumplimiento_parcial_item_tipo_2' => 'nullable|numeric|max:100'          
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
            $exist = $this->checkInspectionExist($data['nombre'], $data['tipo'], $data['cumplimiento_parcial_tipo_1']);

            $exist_theme = $this->checkThemeInspectionExist($data['tema'], $exist);

            $key_casilla = $exist . '-' . $exist_theme;

            if ($data['tipo'] == 'Tipo 2')
            {
                if (!isset($this->sum_compliance[$key_casilla]))
                    $this->sum_compliance[$key_casilla] = $data['valor_cumplimiento_item_tipo_2'];
                else
                    $this->sum_compliance[$key_casilla] += $data['valor_cumplimiento_item_tipo_2'];

                if (!isset($this->sum_parcial[$key_casilla]))
                    $this->sum_parcial[$key_casilla] = $data['valor_cumplimiento_parcial_item_tipo_2'];
                else
                    $this->sum_parcial[$key_casilla] += $data['valor_cumplimiento_parcial_item_tipo_2'];

                if ($this->sum_compliance[$key_casilla] <= 100 && $this->sum_parcial[$key_casilla] <= 100)
                {
                    $item = new InspectionSectionItem;
                    $item->description = $data['item'];
                    $item->inspection_section_id = $exist_theme;
                    $item->compliance_value = $data['valor_cumplimiento_item_tipo_2'];
                    $item->partial_value = $data['valor_cumplimiento_parcial_item_tipo_2'];
        
                    $item->save();
                }
                else
                {
                    $this->sum_compliance[$key_casilla] -= $data['valor_cumplimiento_item_tipo_2'];
                    $this->sum_parcial[$key_casilla] -= $data['valor_cumplimiento_parcial_item_tipo_2'];
                    $this->setError("La sumatoria total del porcentaje total de cumplimiento o de cumplimiento parcial del tema  " . $this->theme_compliance->name . " es mayor a 100%");

                    $this->setErrorData($row);
                }
            }
            else
            {
                $item = new InspectionSectionItem;
                $item->description = $data['item'];
                $item->inspection_section_id = $exist_theme;
    
                $item->save();
            }

            return true;
        }
    }

    private function checkInspectionExist($name, $type, $value_partial)
    {
        if ($type == 'Tipo 1')
            $type = 1;
        else
            $type = 2;

        $inspection = Inspection::query();
        $inspection->company_scope = $this->company_id;
        $inspection = $inspection->firstOrCreate(
            ['name' => $name, 'type_id' => $type, 'company_id' => $this->company_id], 
            ['name' => $name, 'type_id' => $type,'company_id' => $this->company_id]
        );

        if ($inspection->type_id == 1)
        {
            $inspection->fullfilment_parcial = $value_partial;
            $inspection->save();
        }

        $this->saveLocation($inspection, $this->locations);

        return $inspection->id;
    }

    private function checkThemeInspectionExist($name, $inspection_id)
    {
        $theme = InspectionSection::query();
        $theme = $theme->firstOrCreate(
            ['name' => $name, 'inspection_id' => $inspection_id], 
            ['name' => $name, 'inspection_id' => $inspection_id]);

        $this->theme_compliance = $theme;

        return $theme->id;
    }

    private function saveLocation($inspection, $request)
    {

        $regionals = [];
        $headquarters = [];
        $processes = [];
        $areas = [];

        if ($request['employee_regional_id'])
            $regionals = $this->getDataFromMultiselect($request['employee_regional_id']);

        if (isset($request['employee_headquarter_id']))
            $headquarters = $this->getDataFromMultiselect($request['employee_headquarter_id']);

        if (isset($request['employee_process_id']))
            $processes = $this->getDataFromMultiselect($request['employee_process_id']);

        if (isset($request['employee_area_id']))
            $areas = $this->getDataFromMultiselect($request['employee_area_id']);


        $headquarters_valid = $this->getHeadquarter($regionals);
        $headquarters = array_intersect($headquarters, $headquarters_valid);

        $processes_valid = $this->getProcess($headquarters);
        $processes = array_intersect($processes, $processes_valid);

        $areas_valid = $this->getAreas($headquarters, $processes);
        $areas = array_intersect($areas, $areas_valid);

        $inspection->headquarters()->sync($headquarters);
        $inspection->regionals()->sync($regionals);
        $inspection->processes()->sync($processes);
        $inspection->areas()->sync($areas);
    }

    private function getHeadquarter($regionals)
    {
        $headquarters = EmployeeHeadquarter::selectRaw(
            "sau_employees_headquarters.id as id")
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->whereIn('employee_regional_id', $regionals)
        ->pluck('id')
        ->toArray();

        return $headquarters;
    }

    private function getProcess($headquarters)
    {
        $processes = EmployeeProcess::selectRaw(
            "sau_employees_processes.id as id")
        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->whereIn('sau_headquarter_process.employee_headquarter_id', $headquarters)
        ->pluck('id')
        ->toArray();

        return $processes;
    }

    private function getAreas($headquarters, $process)
    {
        $areas = EmployeeArea::selectRaw(
            "sau_employees_areas.id as id")
        ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
        ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
        ->whereIn('employee_headquarter_id', $headquarters)
        ->whereIn('employee_process_id', $process)
        ->pluck('id')
        ->toArray();
    
        return $areas;
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