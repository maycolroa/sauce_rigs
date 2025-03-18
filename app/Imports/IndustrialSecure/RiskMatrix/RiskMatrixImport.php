<?php

namespace App\Imports\IndustrialSecure\RiskMatrix;

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
use App\Models\Administrative\Processes\TagsProcess;
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
    private $riskMatrix;
    private $secuence = [];
    private $count_controls = 0;

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
                    ->message('Se produjo un error durante el proceso de importación de matríz de riesgos. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
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
            $data['name'] = $row[0];
            $data['regional'] = $row[1];
        }
        if ($confLocation['headquarter'] == 'SI')
        {
            $saltos = 2;
            $data['sede'] = $row[2];
        }
        if ($confLocation['process'] == 'SI')
        {
            $saltos = 4;
            $data['proceso'] = $row[3];
            $data['macroproceso'] = $row[4];

        }
        if ($confLocation['area'] == 'SI')
        {
            $saltos = 5;
            $data['area'] = $row[5];
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
                'economico' => $row[8 + $saltos],
                'atencion_paciente' => $row[9 + $saltos],
                'reputacional' => $row[10 + $saltos],
                'legal_Regulatorio' => $row[11 + $saltos],
                'ambiental' => $row[12 + $saltos],
                'max_impacto_inherente' => $row[13 + $saltos],
                'desc_impacto_inherente' => $row[14 + $saltos],
                'max_frecuencia_inherente' => $row[15 + $saltos],
                'desc_frecuencia_inherente' => $row[16 + $saltos],
                'exposicion_inherente' => $row[17 + $saltos],
                'controles' => $row[18 + $saltos],
                'control_disminuir' => $row[19 + $saltos],
                'naturaleza' => $row[20 + $saltos],
                'evidencia' => trim(strtoupper($row[21 + $saltos])),
                'cobertura' => $row[22 + $saltos],
                'documentacion' => $row[23 + $saltos],
                'segregacion' => trim(strtoupper($row[24 + $saltos])),
                'evaluacion_control' => $row[25 + $saltos],
                'mitigacion' => $row[26 + $saltos],
                'max_impacto_residual' => $row[27 + $saltos],
                'desc_max_impacto_residual' => $row[28 + $saltos],
                'max_frecuencia_residual' => $row[29 + $saltos],
                'desc_frecuencia_residual' => $row[30 + $saltos],
                'exposicion_Residual' => $row[31 + $saltos],
                'max_evento_riesgo' => $row[32 + $saltos],
                'indicadores' => $row[33 + $saltos]
            ]);

        if (COUNT($this->secuence) > 0 && isset($this->secuence[$data['secuencia']]))
            $this->createRisk = false;
        else
            $this->createRisk = true;
                

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
                'exposicion_inherente' => 'required|integer|min:0',
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
                'exposicion_Residual' => 'required|integer|min:0',
                'max_evento_riesgo' => 'required',
                'indicadores' => 'nullable'           
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

                    $this->tagsSave($participants, TagsRmParticipant::class, $this->company_id);
                //

                if ($createMatrix)
                {
                    $regional_id = $confLocation['regional'] == 'SI' ? $this->checkRegional($data['regional']) : null;
                    $macroprocess_id = $confLocation['process'] == 'SI' ? $this->checkMacroprocess($data['macroproceso']) : null;
                    $headquarter_id = $confLocation['headquarter'] == 'SI' ? $this->checkHeadquarter($regional_id, $data['sede']) : null;
                    $process_id = $confLocation['process'] == 'SI' ? $this->checkProcess($headquarter_id, $data['proceso']) : null; 
                    $area_id = $confLocation['area'] == 'SI' ? $this->checkArea($headquarter_id, $process_id, $data['area']) : null;

                    $this->riskMatrix = new RiskMatrix();
                    $this->riskMatrix->company_id = $this->company_id;
                    $this->riskMatrix->user_id = $this->user->id;
                    $this->riskMatrix->name = $data['name'];
                    $this->riskMatrix->employee_regional_id = $regional_id;
                    $this->riskMatrix->employee_headquarter_id = $headquarter_id;
                    $this->riskMatrix->employee_area_id = $area_id;
                    $this->riskMatrix->employee_process_id = $process_id;
                    $this->riskMatrix->macroprocess_id = $macroprocess_id;
                    $this->riskMatrix->participants = $participants->implode(',');
                    $this->riskMatrix->nomenclature = $data['nomenclatura'];
                    $this->riskMatrix->save();
                }

                $sub_process = SubProcess::query();
                $sub_process->company_scope = $this->company_id;
                $sub_process = $sub_process->firstOrCreate(
                    ['name' => $data['sub_proceso']], 
                    ['name' => $data['sub_proceso'], 'company_id' => $this->company_id]
                );

                $risk = Risk::query();
                $risk->company_scope = $this->company_id;
                $risk = $risk->firstOrCreate(
                    ['name' => $data['riesgo'], 'category' => $data['categoria']], 
                    ['name' => $data['riesgo'], 'category' => $data['categoria'], 'company_id' => $this->company_id]
                );

                $matrixSubprocess = RiskMatrixSubProcess::query();
                $matrixSubprocess->company_scope = $this->company_id;
                $matrixSubprocess = $matrixSubprocess->firstOrCreate(
                    ['risk_matrix_id' => $this->riskMatrix->id, 'sub_process_id' => $sub_process->id], 
                    ['risk_matrix_id' => $this->riskMatrix->id, 'sub_process_id' => $sub_process->id]
                );

                $risk_subproocess = new SubProcessRisk();
                $risk_subproocess->rm_subprocess_id = $matrixSubprocess->id;
                $risk_subproocess->risk_id = $risk->id;
                $risk_subproocess->nomenclature = $data['nomenclatura'] . '.R.' . $data['secuencia'];
                $risk_subproocess->risk_sequence = $data['secuencia'];
                $risk_subproocess->economic = $data['economico'];
                $risk_subproocess->quality_care_patient_safety =  $data['atencion_paciente'];
                $risk_subproocess->reputational = $data['reputacional'];
                $risk_subproocess->legal_regulatory = $data['legal_Regulatorio'];
                $risk_subproocess->environmental = $data['ambiental'];
                $risk_subproocess->max_inherent_impact = $data['max_impacto_inherente'];
                $risk_subproocess->description_inherent_impact = trim($data['desc_impacto_inherente']);
                $risk_subproocess->max_inherent_frequency = $data['max_frecuencia_inherente'];            
                $risk_subproocess->description_inherent_frequency = trim($data['desc_frecuencia_inherente']);
                $risk_subproocess->inherent_exposition = $data['exposicion_inherente'];
                $risk_subproocess->controls_decrease = $data['control_disminuir'];
                $risk_subproocess->nature = $data['naturaleza'];
                $risk_subproocess->evidence = $data['evidencia'];
                $risk_subproocess->coverage = $data['cobertura'];
                $risk_subproocess->documentation = $data['documentacion'];
                $risk_subproocess->segregation = $data['segregacion'];
                $risk_subproocess->control_evaluation = $data['evaluacion_control'];
                $risk_subproocess->percentege_mitigation = $data['mitigacion'];
                $risk_subproocess->max_residual_impact = $data['max_impacto_residual'];
                $risk_subproocess->description_residual_impact = trim($data['desc_max_impacto_residual']);
                $risk_subproocess->max_residual_frequency = $data['max_frecuencia_residual'];
                $risk_subproocess->description_residual_frequency = trim($data['desc_frecuencia_residual']);
                $risk_subproocess->residual_exposition = $data['exposicion_Residual'];
                $risk_subproocess->max_impact_event_risk = $data['max_evento_riesgo'];
                $risk_subproocess->save();

                $cause = new Cause();
                $cause->rm_subprocess_risk_id = $risk_subproocess->id;
                $cause->cause = $data['causa'];
                $cause->save();

                $controls = explode('*', $data['controles']);

                foreach ($controls as $keyC2 => $itemC2)
                {
                    $this->count_controls = $this->count_controls + 1;

                    $control = new CauseControl();
                    $control->controls = $itemC2;
                    $control->rm_cause_id = $cause->id;
                    $control->number_control = $this->count_controls;
                    $control->nomenclature = $data['nomenclatura']. '.C.' . $this->count_controls;
                    $control->save();
                }

                if ($data['indicadores'])
                {
                    $indicators =explode('*', $data['indicadores']);

                    foreach ($indicators as $keyI => $itemI)
                    {
                        $indicator = new Indicators();
                        $indicator->rm_subprocess_risk_id = $risk_subproocess->id;
                        $indicator->indicator = $itemI;
                        $indicator->save();
                    }
                }

                $this->secuence[$data['secuencia']] = $risk_subproocess->id;
            }
            else
            {
                $cause = new Cause();
                $cause->rm_subprocess_risk_id = $this->secuence[$data['secuencia']];
                $cause->cause = $data['causa'];
                $cause->save();

                $controls = explode('*', $data['controles']);

                foreach ($controls as $keyC2 => $itemC2)
                {
                    $this->count_controls = $this->count_controls + 1;

                    $control = new CauseControl();
                    $control->controls = $itemC2;
                    $control->rm_cause_id = $cause->id;
                    $control->number_control = $this->count_controls;
                    $control->nomenclature = $data['nomenclatura']. '.C.' . $this->count_controls;
                    $control->save();
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
            //$process->types = $macroproceso;
            $process->save();
        }
        else
        {
            $process = EmployeeProcess::find($process->id);
            //$process->types = $macroproceso;
            $process->save();
        }
        
        $process->headquarters()->detach($headquarter_id);
        $process->headquarters()->attach($headquarter_id);

        return $process->id;
    }

    private function checkMacroprocess($name)
    {
        $macroprocess = TagsProcess::query();
        $macroprocess->company_scope = $this->company_id;
        $macroprocess = $macroprocess->firstOrCreate(['name' => $name], 
                                            ['name' => $name, 'company_id' => $this->company_id]);

        return $macroprocess->id;
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