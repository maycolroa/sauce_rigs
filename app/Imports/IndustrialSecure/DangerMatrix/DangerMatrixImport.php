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
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\TagsAdministrativeControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use App\Models\IndustrialSecure\DangerMatrix\TagsEngineeringControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEpp;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsSubstitution;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use App\Models\Administrative\Processes\TagsProcess;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\Activities\Activity;
use App\Models\IndustrialSecure\Dangers\Danger;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use App\Traits\ConfigurableFormTrait;
use App\Traits\LocationFormTrait;
use App\Traits\DangerMatrixTrait;

class DangerMatrixImport implements ToCollection, WithCalculatedFormulas
{
    use ConfigurableFormTrait;
    use LocationFormTrait;
    use DangerMatrixTrait;

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
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkDangerMarix($row, $key == 1);
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
                    $nameExcel = 'export/1/danger_matrix_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new DangerMatrixImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de matríz de peligros')
                        ->recipients($this->user)
                        ->message('El proceso de importación de matríz de peligros finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
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
                    subject('Importación de matríz de peligros')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de matríz de peligros. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
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

    private function checkDangerMarix($row, $createMatrix)
    {
        $data = [];
        $saltos = 0;
        $confLocation = $this->getLocationFormConfModule($this->company_id);

        $conf = QualificationCompany::select('qualification_id');
        $conf->company_scope = $this->company_id;
        $conf = $conf->first();

        if ($conf && $conf->qualification)
            $conf = $conf->qualification;
        else
            $conf = Qualification::where('name', $this->getDefaultCalificationDm())->first();

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
                'participantes' => $row[0 + $saltos],
                'actividad' => $row[1 + $saltos],
                'tipo_de_actividad' => trim(strtoupper($row[2 + $saltos])),
                'peligro' => $row[3 + $saltos],
                'descripción_del_peligro' => $row[4 + $saltos],
                'peligro_generado' => $row[5 + $saltos],
                'posibles_consecuencias' => $row[6 + $saltos],
                'fuente_generadora' => $row[7 + $saltos],
                'colaboradores' => $row[8 + $saltos],
                'contratistas' => $row[9 + $saltos],
                'visitantes' => $row[10 + $saltos],
                'estudiantes' => $row[11 + $saltos],
                'arrendatarios' => $row[12 + $saltos],
                'observaciones' => $row[13 + $saltos],
                'controles_de_ingeniería' => $row[14 + $saltos],
                'sustitución' => $row[15 + $saltos],
                'señalización_advertencia' => $row[16 + $saltos],
                'controles_administrativos' => $row[17 + $saltos],
                'epp' => $row[18 + $saltos],
                'cumplimiento_requisitos_legales' => trim(strtoupper($row[19 + $saltos])),
                'alineamiento_con_las_políticas' => trim(strtoupper($row[20 + $saltos])),
                'alineamiento_con_los_objetivos' => trim(strtoupper($row[21 + $saltos])),
                'eliminación_medidas' => $row[22 + $saltos],
                'sustitución_medidas' => $row[23 + $saltos],
                'controles_de_ingeniería_medidas' => $row[24 + $saltos],
                'señalización_advertencia_medidas' => $row[25 + $saltos],
                'controles_administrativos_medidas' => $row[26 + $saltos],
                'epp_medidas' => $row[27 + $saltos]
            ]);

        if ($conf->name == 'Tipo 1')
        {
            $data = array_merge($data,
            [
                'nivel_de_probabilidad' => trim(ucfirst($row[28 + $saltos])),
                'nr_personas' => $row[29 + $saltos],
                'nr_económico' => $row[30 + $saltos],
                'nr_imagen' => $row[31 + $saltos]
            ]);
        }
        else if ($conf->name == 'Tipo 2')
        {
            $data = array_merge($data,
            [
                'frecuencia' => strtoupper($row[28 + $saltos]),
                'severidad' => strtoupper($row[29 + $saltos])
            ]);
        }

        /*$ndp = $data["nivel_de_probabilidad"] == 'Sucede varias veces en el último año y en diferentes procesos' ? 'Sucede varias veces en el último año y en diferentes procesos (en el hospital)' : $data["nivel_de_probabilidad"];
        \Log::info($ndp);
        $sql = Employee::where('identification', $data['identificacion']);
        $sql->company_scope = $this->company_id;
        $employee = $sql->first();*/

        $rules = [];

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
            'cumplimiento_requisitos_legales' => 'required|in:SI,NO',
            'alineamiento_con_las_políticas' => 'required|in:SI,NO',
            'alineamiento_con_los_objetivos' => 'required|in:SI,NO'            
        ]);

        $rulesDm = $this->getRulesDmImport($conf->name);

        if ($conf->name == 'Tipo 1')
        {
            $level = $rulesDm["Nivel de Probabilidad"];

            $rules = array_merge($rules,
                [
                    'nivel_de_probabilidad' => "required|in:".implode(",", $level)
                ]);

            if (isset($level[$data["nivel_de_probabilidad"]]))
            {
                $current_level = $data["nivel_de_probabilidad"];
                $rules = array_merge($rules,
                [
                    'nr_personas' => "required|integer|in:".implode(",", $rulesDm["NR Personas"][$current_level]),
                    'nr_económico' => "required|integer|in:".implode(",", $rulesDm["NR Económico"][$current_level]),
                    'nr_imagen' => "required|integer|in:".implode(",", $rulesDm["NR Imagen"][$current_level])
                ]);
            }
            else
            {
                $rules = array_merge($rules,
                    [
                        'nr_personas' => 'required|integer',
                        'nr_económico' => 'required|integer',
                        'nr_imagen' => 'required|integer'
                    ]);
            }
        }
        else if ($conf->name == 'Tipo 2')
        {
            $level = $rulesDm["Frecuencia"];
            $level2 = $rulesDm["Severidad"];

            $rules = array_merge($rules,
                [
                    'frecuencia' => "required|in:".implode(",", $level),
                    'severidad' => "required|in:".implode(",", $level2)
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
            //TAGS
                $possible_consequences_danger = $this->tagsPrepareImport($data['posibles_consecuencias']);
                $danger_description = $this->tagsPrepareImport($data['descripción_del_peligro']);
                $existing_controls_engineering_controls = $this->tagsPrepareImport($data['controles_de_ingeniería']);
                $existing_controls_substitution = $this->tagsPrepareImport($data['sustitución']);
                $existing_controls_warning_signage = $this->tagsPrepareImport($data['señalización_advertencia']);
                $existing_controls_administrative_controls = $this->tagsPrepareImport($data['controles_administrativos']);
                $existing_controls_epp = $this->tagsPrepareImport($data['epp']);
                $intervention_measures_substitution = $this->tagsPrepareImport($data['sustitución_medidas']);
                $intervention_measures_engineering_controls = $this->tagsPrepareImport($data['controles_de_ingeniería_medidas']);
                $intervention_measures_warning_signage = $this->tagsPrepareImport($data['señalización_advertencia_medidas']);
                $intervention_measures_administrative_controls = $this->tagsPrepareImport($data['controles_administrativos_medidas']);
                $intervention_measures_epp = $this->tagsPrepareImport($data['epp_medidas']);
                $participants = $this->tagsPrepareImport($data['participantes']);

                if ($confLocation['process'] == 'SI')
                    $macroproceso = $this->tagsPrepareImport($data['macroproceso']);



                $this->tagsSave($possible_consequences_danger, TagsPossibleConsequencesDanger::class, $this->company_id);
                $this->tagsSave($danger_description, TagsDangerDescription::class, $this->company_id);
                $this->tagsSave($existing_controls_engineering_controls, TagsEngineeringControls::class, $this->company_id);
                $this->tagsSave($existing_controls_substitution, TagsSubstitution::class, $this->company_id);
                $this->tagsSave($existing_controls_warning_signage, TagsWarningSignage::class, $this->company_id);
                $this->tagsSave($existing_controls_administrative_controls, TagsAdministrativeControls::class, $this->company_id);
                $this->tagsSave($existing_controls_epp, TagsEpp::class, $this->company_id);
                $this->tagsSave($intervention_measures_substitution, TagsSubstitution::class, $this->company_id);
                $this->tagsSave($intervention_measures_engineering_controls, TagsEngineeringControls::class, $this->company_id);
                $this->tagsSave($intervention_measures_warning_signage, TagsWarningSignage::class, $this->company_id);
                $this->tagsSave($intervention_measures_administrative_controls, TagsAdministrativeControls::class, $this->company_id);
                $this->tagsSave($intervention_measures_epp, TagsEpp::class, $this->company_id);
                $this->tagsSave($participants, TagsParticipant::class, $this->company_id);

                if ($confLocation['process'] == 'SI')
                    $this->tagsSave($macroproceso, TagsProcess::class, $this->company_id);


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

            if ($conf->name == 'Tipo 1')
            {
                $qualification1 = new QualificationDanger();
                $qualification1->activity_danger_id = $activityDanger->id;
                $qualification1->type_id = $conf->types()->where('description', 'Nivel de Probabilidad')->first()->id;
                $qualification1->value_id = $data["nivel_de_probabilidad"];
                $qualification1->save();

                $qualification2 = new QualificationDanger();
                $qualification2->activity_danger_id = $activityDanger->id;
                $qualification2->type_id = $conf->types()->where('description', 'NR Personas')->first()->id;
                $qualification2->value_id = $data["nr_personas"];
                $qualification2->save();
                $nri = $data["nr_personas"];

                $qualification3 = new QualificationDanger();
                $qualification3->activity_danger_id = $activityDanger->id;
                $qualification3->type_id = $conf->types()->where('description', 'NR Económico')->first()->id;
                $qualification3->value_id = $data["nr_económico"];
                $qualification3->save();
                $nri = $data["nr_económico"] > $nri ? $data["nr_económico"] : $nri;

                $qualification4 = new QualificationDanger();
                $qualification4->activity_danger_id = $activityDanger->id;
                $qualification4->type_id = $conf->types()->where('description', 'NR Imagen')->first()->id;
                $qualification4->value_id = $data["nr_imagen"];
                $qualification4->save();
                $nri = $data["nr_imagen"] > $nri ? $data["nr_imagen"] : $nri;

                $qualification5 = new QualificationDanger();
                $qualification5->activity_danger_id = $activityDanger->id;
                $qualification5->type_id = $conf->types()->where('description', 'NRI')->first()->id;
                $qualification5->value_id = $nri;
                $qualification5->save();

                $matriz_calification = $this->getMatrixCalification($conf->name);

                $ndp = $data["nivel_de_probabilidad"];

                if (isset($matriz_calification[$ndp]) && isset($matriz_calification[$ndp][$nri]))
                {
                    $activityDanger->qualification = $matriz_calification[$ndp][$nri]['label'];
                    $activityDanger->save();
                }
            }
            else if ($conf->name == 'Tipo 2')
            {
                $qualification1 = new QualificationDanger();
                $qualification1->activity_danger_id = $activityDanger->id;
                $qualification1->type_id = $conf->types()->where('description', 'Frecuencia')->first()->id;
                $qualification1->value_id = $data["frecuencia"];
                $qualification1->save();
                $fre = $data["frecuencia"];

                $qualification2 = new QualificationDanger();
                $qualification2->activity_danger_id = $activityDanger->id;
                $qualification2->type_id = $conf->types()->where('description', 'Severidad')->first()->id;
                $qualification2->value_id = $data["severidad"];
                $qualification2->save();
                $sev = $data["severidad"];

                $matriz_calification = $this->getMatrixCalification($conf->name);

                if (isset($matriz_calification[$sev]) && isset($matriz_calification[$sev][$fre]))
                {
                    $activityDanger->qualification = $matriz_calification[$sev][$fre]['label'];
                    $activityDanger->save();
                }
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