<?php

namespace App\Imports\IndustrialSecure\DangerMatrix;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrixActivity;
use App\Models\IndustrialSecure\DangerMatrix\QualificationDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsAdministrativeControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use App\Models\IndustrialSecure\DangerMatrix\TagsEngineeringControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEpp;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsSubstitution;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use App\Models\IndustrialSecure\Activities\Activity;
use App\Models\IndustrialSecure\Dangers\Danger;
use App\Facades\Configuration;
use App\Exports\Administrative\Employees\EmployeeImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use Exception;
use App\Traits\ConfigurableFormTrait;
use App\Traits\LocationFormTrait;

class DangerMatrixImport implements ToCollection
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
    private $dangerMatrix;

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
                        if (COUNT($row) == 42 || COUNT($row) == 43)
                        {
                            $this->checkDangerMarix($row, $key == 1);
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
                        subject('Importación de matríz de peligro')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de la matríz de peligro finalizo correctamente')
                        ->module('dangerMatrix')
                        ->event('Job: DangerMatrixImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/empleados_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    /*Excel::store(new EmployeeImportErrorExcel(collect($this->errors_data), $this->errors, $this->formModel, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
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
                        ->send();*/
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

    private function checkDangerMarix($row, $createMatrix)
    {
        $data = [
            'regional' => $row[0],
            'sede' => $row[1],
            'proceso' => $row[2],
            'area' => $row[3],
            'participantes' => $row[4],
            'actividad' => $row[5],
            'tipo_de_actividad' => strtoupper($row[6]),
            'peligro' => $row[7],
            'descripción_del_peligro' => $row[8],
            'peligro_generado' => $row[9],
            'posibles_consecuencias' => $row[10],
            'fuente_generadora' => $row[11],
            'colaboradores' => $row[12],
            'contratistas' => $row[13],
            'visitantes' => $row[14],
            'estudiantes' => $row[15],
            'arrendatarios' => $row[16],
            'controles_de_ingeniería' => $row[17],
            'sustitución' => $row[18],
            'señalización_advertencia' => $row[19],
            'controles_administrativos' => $row[20],
            'epp' => $row[21],
            'cumplimiento_requisitos_legales' => strtoupper($row[22]),
            'alineamiento_con_las_políticas' => strtoupper($row[23]),
            'alineamiento_con_los_objetivos' => strtoupper($row[24]),
            'eliminación_medidas' => $row[25],
            'sustitución_medidas' => $row[26],
            'controles_de_ingeniería_medidas' => $row[27],
            'señalización_advertencia_medidas' => $row[28],
            'controles_administrativos_medidas' => $row[29],
            'epp_medidas' => $row[30],
            'nivel_de_probabilidad' => $row[31],
            'nr_personas' => $row[32],
            'nr_económico' => $row[33],
            'nr_imagen' => $row[34]
        ];

        \Log::info(strtoupper($row[22]));
        \Log::info(strtoupper($row[23]));
        \Log::info(strtoupper($row[24]));


        /*$sql = Employee::where('identification', $data['identificacion']);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();*/

        $rules = [];
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        if ($createMatrix)
        {
            if ($confLocation['regional'] == 'SI')
                $rules['regional'] = 'required';
            if ($confLocation['headquarter'] == 'SI')
                $rules['sede'] = 'required';
            if ($confLocation['process'] == 'SI')
                $rules['proceso'] = 'required';
            if ($confLocation['area'] == 'SI')
                $rules['area'] = 'required';
        }
    
        $rules = array_merge($rules,
        [
            'actividad' => 'required',
            'tipo_de_actividad' => 'required|in:R,NR',
            'peligro' => 'required',
            'peligro_generado' => 'required',
            'posibles_consecuencias' => 'required',
            'fuente_generadora' => 'required',
            'colaboradores' => 'required|integer|min:0',
            'contratistas' => 'required|integer|min:0',
            'visitantes' => 'required|integer|min:0',
            'estudiantes' => 'required|integer|min:0',
            'arrendatarios' => 'required|integer|min:0',
            'nivel_de_probabilidad' => 'required',
            'nr_personas' => 'required',
            'nr_económico' => 'required',
            'nr_imagen' => 'required',
            'cumplimiento_requisitos_legales' => 'required|in:SI,NO',
            'alineamiento_con_las_políticas' => 'required|in:SI,NO',
            'alineamiento_con_los_objetivos' => 'required|in:SI,NO'            
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
            if ($createMatrix)
            {
                $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional']) : null;
                $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
                $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso']) : null;
                $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

                $this->dangerMatrix = new DangerMatrix();
                $this->dangerMatrix->company_id = $this->company_id;
                $this->dangerMatrix->user_id = $this->user->id;
                $this->dangerMatrix->employee_regional_id = $regional_id;
                $this->dangerMatrix->employee_headquarter_id = $headquarter_id;
                $this->dangerMatrix->employee_area_id = $area_id;
                $this->dangerMatrix->employee_process_id = $process_id;
                $this->dangerMatrix->save();
            }

            $activity = Activity::query();
            $activity->company_scope = $this->company_id;
            $activity = $activity->firstOrCreate(['name' => $data['actividad']], 
                                                ['name' => $data['actividad'], 'company_id' => $this->company_id]);

            $danger = Danger::query();
            $danger->company_scope = $this->company_id;
            $danger = $danger->firstOrCreate(['name' => $data['peligro']], 
                                                ['name' => $data['peligro'], 'company_id' => $this->company_id]);

            $danger = DangerMatrixActivity::query();
            $danger->company_scope = $this->company_id;
            $danger = $danger->firstOrCreate(
                ['danger_matrix_id' => $this->dangerMatrix->id, 'activity_id' => $activity->id, 'type_activity' => $data['tipo_de_actividad'] == 'R' ? 'Rutinaria' : 'No rutinaria'], 
                ['danger_matrix_id' => $this->dangerMatrix->id, 'activity_id' => $activity->id, 'type_activity' => $data['tipo_de_actividad'] == 'R' ? 'Rutinaria' : 'No rutinaria']
                );


            $activityDanger = new ActivityDanger();
            $activityDanger->dm_activity_id = $activity->id;
            $activityDanger->danger_id = $danger->id;
            $activityDanger->danger_description = $data['descripción_del_peligro'];
            $activityDanger->danger_generated = $data['peligro_generado'];
            $activityDanger->possible_consequences_danger = $data['posibles_consecuencias'];
            $activityDanger->generating_source = $data['fuente_generadora'];
            $activityDanger->collaborators_quantity = $data['colaboradores'];
            $activityDanger->esd_quantity = $data['contratistas'];
            $activityDanger->visitor_quantity = $data['visitantes'];
            $activityDanger->student_quantity = $data['estudiantes'];
            $activityDanger->esc_quantity = $data['arrendatarios'];
            $activityDanger->existing_controls_engineering_controls = $data['controles_de_ingeniería'];
            $activityDanger->existing_controls_substitution = $data['sustitución'];
            $activityDanger->existing_controls_warning_signage = $data['señalización_advertencia'];
            $activityDanger->existing_controls_administrative_controls = $data['controles_administrativos'];
            $activityDanger->existing_controls_epp = $data['epp'];
            $activityDanger->legal_requirements = $data['cumplimiento_requisitos_legales'];
            $activityDanger->quality_policies = $data['alineamiento_con_las_políticas'];
            $activityDanger->objectives_goals = $data['alineamiento_con_los_objetivos'];
            $activityDanger->intervention_measures_elimination = $data['eliminación_medidas'];
            $activityDanger->intervention_measures_substitution = $data['sustitución_medidas'];
            $activityDanger->intervention_measures_engineering_controls = $data['controles_de_ingeniería_medidas'];
            $activityDanger->intervention_measures_warning_signage = $data['señalización_advertencia_medidas'];
            $activityDanger->intervention_measures_administrative_controls = $data['controles_administrativos_medidas'];
            $activityDanger->intervention_measures_epp = $data['epp_medidas'];

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