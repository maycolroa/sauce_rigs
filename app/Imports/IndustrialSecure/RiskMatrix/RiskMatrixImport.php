<?php

namespace App\Imports\IndustrialSecure\DangerMatrix;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Models\IndustrialSecure\RiskMatrix\TagsRmParticipant;
use App\Models\IndustrialSecure\RiskMatrix\Cause;
use App\Models\IndustrialSecure\RiskMatrix\CauseControl;
use App\Models\IndustrialSecure\RiskMatrix\Indicators;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrixSubProcess;
use App\Models\IndustrialSecure\RiskMatrix\SubProcessRisk;
use App\Models\IndustrialSecure\RiskMatrix\Risk;
use App\Models\IndustrialSecure\RiskMatrix\SubProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use App\Traits\ConfigurableFormTrait;
use App\Traits\LocationFormTrait;

class RiskMatrixImport implements ToCollection, WithCalculatedFormulas
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
    private $createRisk;
    private $secuence;

    public function __construct($company_id, $user)
    {
        
      $this->user = $user;
      $this->company_id = $company_id;
      $this->keywords = $this->getKeywordQueue($this->company_id);
      $this->secuence = collect([]);
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
                            $this->checkRiskMarix($row, $key == 1);
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
                        subject('Importación de matríz de riesgos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de la matríz de riesgos finalizo correctamente')
                        ->module('riskMatrix')
                        ->event('Job: RiskMatrixImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/risk_matrix_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new RiskMatrixImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de matríz de riesgos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de matríz de riesgos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('riskMatrix')
                        ->event('Job: RiskMatrixImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de matríz de riesgos')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de matríz de riesgos. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('riskMatrix')
                    ->event('Job: RiskMatrixImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkRiskMarix($row, $createMatrix)
    {
        $data = [];
        $saltos = 0;
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        if ($confLocation['regional'] == 'SI')
        {
            $saltos = 1;
            $data['regional'] = $row[0];
        }
        if ($confLocation['headquarter'] == 'SI')
        {
            $saltos = 2;
            $data['sede'] = $row[1];
        }
        if ($confLocation['process'] == 'SI')
        {
            $saltos = 4;
            $data['proceso'] = $row[2];
            $data['macroproceso'] = $row[3];

        }
        if ($confLocation['area'] == 'SI')
        {
            $saltos = 5;
            $data['area'] = $row[4];
        }

        $data = array_merge($data,
            [  
                'secuencia' => $row[1 + $saltos],
                'nomenclatura' => $row[2 + $saltos],
                'participantes' => $row[3 + $saltos],
                'sub_proceso' => $row[4 + $saltos],
                'riesgo' => $row[5 + $saltos],
                'categoria' => $row[6 + $saltos],
                'causa' => $row[7 + $saltos],
                'controles' => $row[8 + $saltos],
                'economico' => $row[9 + $saltos],
                'atencion_paciente' => $row[10 + $saltos],
                'reputacional' => $row[11 + $saltos],
                'legal_Regulatorio' => $row[12 + $saltos],
                'ambiental' => $row[13 + $saltos],
                'max_impacto_inherente' => $row[14 + $saltos],
                'desc_impacto_inherente' => $row[15 + $saltos],
                'max_frecuencia_inherente' => $row[15 + $saltos],
                'desc_frecuencia_inherente' => $row[16 + $saltos],
                'exposicion_inherente' => $row[17 + $saltos],
                'control_disminuir' => $row[18 + $saltos],
                'naturaleza' => $row[19 + $saltos],
                'evidencia' => trim(strtoupper($row[20 + $saltos])),
                'cobertura' => $row[21 + $saltos],
                'documentacion' => $row[22 + $saltos],
                'segregacion' => trim(strtoupper($row[23 + $saltos])),
                'evaluacion_control' => $row[24 + $saltos],
                'mitigacion' => $row[25 + $saltos],
                'max_impacto_residual' => $row[26 + $saltos],
                'desc_max_impacto_residual' => $row[27 + $saltos],
                'max_frecuencia_residual' => $row[28 + $saltos],
                'desc_frecuencia_residual' => $row[29 + $saltos],
                'exposicion_Residual' => $row[30 + $saltos],
                'max_evento_riesgo' => $row[31 + $saltos],
                'indicadores' => $row[32 + $saltos]
            ]);

        if ($this->secuence['R.'.$data['secuencia']])
                $this->createRisk = false;
        else
            $this->createRisk = false;
                

        $rules = [];

        if ($this->createRisk)
        {
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
                'secuencia' => 'required|integer',
                'nomenclatura' => 'required',
                'participantes' => 'required',
                'sub_proceso' => 'required',
                'riesgo' => 'required',
                'categoria' => 'required',
                'causa' => 'required',
                'controles' => 'required',
                'economico' => 'required|integer|min:0|max:5',
                'atencion_paciente' => 'required|integer|min:0|max:5',
                'reputacional' => 'required|integer|min:0|max:5',
                'legal_Regulatorio' => 'required|integer|min:0|max:5',
                'ambiental' => 'required|integer|min:0|max:5',
                'max_impacto_inherente' => 'required|integer|min:0|max:5',
                'desc_impacto_inherente' => 'required',
                'max_frecuencia_inherente' => 'required|integer|min:0|max:5',
                'desc_frecuencia_inherente' => 'required',
                'exposicion_inherente' => 'required|integer|min:0|max:5',
                'control_disminuir' => 'required',
                'naturaleza' => 'required',
                'evidencia' => 'required|in:SI,NO',
                'cobertura' => 'required',
                'documentacion' => 'required',
                'segregacion' => 'required|in:SI,NO',
                'evaluacion_control' => 'required',
                'mitigacion' => 'required',
                'max_impacto_residual' => 'required|integer|min:0|max:5',
                'desc_max_impacto_residual' => 'required',
                'max_frecuencia_residual' => 'required|integer|min:0|max:5',
                'desc_frecuencia_residual' => 'required',
                'exposicion_Residual' => 'required|integer|min:0|max:5',
                'max_evento_riesgo' => 'required',
                'indicadores' => 'required'           
            ]);
        }

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
            if ($this->createRisk)
            {
                //TAGS
                    $participants = $this->tagsPrepareImport($data['participantes']);

                    $this->tagsSave($participants, TagsParticipant::class, $this->company_id);
                //

                if ($createMatrix)
                {
                    $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional']) : null;
                    $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
                    $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso'], $macroproceso->implode(',')) : null; 
                    $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

                    $this->dangerMatrix = new DangerMatrix();
                    $this->dangerMatrix->company_id = $this->company_id;
                    $this->dangerMatrix->user_id = $this->user->id;
                    $this->dangerMatrix->employee_regional_id = $regional_id;
                    $this->dangerMatrix->employee_headquarter_id = $headquarter_id;
                    $this->dangerMatrix->employee_area_id = $area_id;
                    $this->dangerMatrix->employee_process_id = $process_id;
                    $this->dangerMatrix->participants = $participants->implode(',');
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

                $matrixActivity = DangerMatrixActivity::query();
                $matrixActivity->company_scope = $this->company_id;
                $matrixActivity = $matrixActivity->firstOrCreate(
                    ['danger_matrix_id' => $this->dangerMatrix->id, 'activity_id' => $activity->id, 'type_activity' => $data['tipo_de_actividad'] == 'R' ? 'Rutinaria' : 'No rutinaria'], 
                    ['danger_matrix_id' => $this->dangerMatrix->id, 'activity_id' => $activity->id, 'type_activity' => $data['tipo_de_actividad'] == 'R' ? 'Rutinaria' : 'No rutinaria']
                    );

                $activityDanger = new ActivityDanger();
                $activityDanger->dm_activity_id = $matrixActivity->id;
                $activityDanger->danger_id = $danger->id;
                $activityDanger->danger_description = $danger_description->implode(',');
                $activityDanger->danger_generated = $data['peligro_generado'];
                $activityDanger->possible_consequences_danger = $possible_consequences_danger->implode(',');
                $activityDanger->generating_source = $data['fuente_generadora'];
                $activityDanger->collaborators_quantity = $data['colaboradores'];
                $activityDanger->esd_quantity = $data['contratistas'];
                $activityDanger->visitor_quantity = $data['visitantes'];
                $activityDanger->student_quantity = $data['estudiantes'];
                $activityDanger->esc_quantity = $data['arrendatarios'];            
                $activityDanger->observations = $data['observaciones'];
                $activityDanger->existing_controls_engineering_controls = $existing_controls_engineering_controls->implode(',');
                $activityDanger->existing_controls_substitution = $existing_controls_substitution->implode(',');
                $activityDanger->existing_controls_warning_signage = $existing_controls_warning_signage->implode(',');
                $activityDanger->existing_controls_administrative_controls = $existing_controls_administrative_controls->implode(',');
                $activityDanger->existing_controls_epp = $existing_controls_epp->implode(',');
                $activityDanger->legal_requirements = $data['cumplimiento_requisitos_legales'];
                $activityDanger->quality_policies = $data['alineamiento_con_las_políticas'];
                $activityDanger->objectives_goals = $data['alineamiento_con_los_objetivos'];
                $activityDanger->risk_acceptability = $data['cumplimiento_requisitos_legales'];
                $activityDanger->intervention_measures_elimination = $data['eliminación_medidas'];
                $activityDanger->intervention_measures_substitution = $intervention_measures_substitution->implode(',');
                $activityDanger->intervention_measures_engineering_controls = $intervention_measures_engineering_controls->implode(',');
                $activityDanger->intervention_measures_warning_signage = $intervention_measures_warning_signage->implode(',');
                $activityDanger->intervention_measures_administrative_controls = $intervention_measures_administrative_controls->implode(',');
                $activityDanger->intervention_measures_epp = $intervention_measures_epp->implode(',');
                $activityDanger->save();
            }
            else
            {
                //
            }

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

    private function checkProcess($headquarter_id, $process_name, $macroproceso)
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
            $process->types = $macroproceso;
            $process->save();
        }
        else
        {
            $process = EmployeeProcess::find($process->id);
            $process->types = $macroproceso;
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