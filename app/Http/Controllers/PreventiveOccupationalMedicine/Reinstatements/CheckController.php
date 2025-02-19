<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\Check\Facades\CheckManager;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\CheckFile;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\TagsReinstatementCondition;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\TagsInformantRole;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\TagsMotiveClose;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\LetterHistory;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborNotes;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\CheckExportJob;
use Illuminate\Support\Facades\Storage;
use App\Traits\ConfigurableFormTrait;
use App\Facades\Mail\Facades\NotificationMail;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use DB;
use PDF;
use Datetime;
use Validator;

class CheckController extends Controller
{ 
    use ConfigurableFormTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_checks_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:reinc_checks_r, {$this->team}");
        $this->middleware("permission:reinc_checks_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:reinc_checks_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:reinc_checks_export, {$this->team}", ['only' => 'export']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $checks = Check::select(
                    'sau_reinc_checks.id AS id',
                    'sau_reinc_checks.deadline AS deadline',
                    'sau_reinc_checks.next_date_tracking AS next_date_tracking',
                    'sau_reinc_cie10_codes.code AS code',
                    'sau_reinc_checks.disease_origin AS disease_origin',
                    'sau_employees_regionals.name AS regional',
                    'sau_reinc_checks.state AS state',
                    'sau_employees.identification AS identification',
                    'sau_employees.name AS name'
                )
                ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
                ->orderBy('sau_reinc_checks.id', 'DESC');

        if ($request->current_check_id)
            $checks->where('sau_reinc_checks.id', '<>', $request->current_check_id);

        if ($request->employee_id)
            $checks->where('sau_reinc_checks.employee_id', '=', $request->employee_id);

        $url = "/preventiveoccupationalmedicine/reinstatements/checks";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["identifications"]))
                $checks->inIdentifications($this->getValuesForMultiselect($filters["identifications"]), $filters['filtersType']['identifications']);

            if (isset($filters["names"]))
                $checks->inNames($this->getValuesForMultiselect($filters["names"]), $filters['filtersType']['names']);

            if (isset($filters["regionals"]))
                $checks->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["businesses"]))
                $checks->inBusinesses($this->getValuesForMultiselect($filters["businesses"]), $filters['filtersType']['businesses']);

            if (isset($filters["diseaseOrigin"]))
                $checks->inDiseaseOrigin($this->getValuesForMultiselect($filters["diseaseOrigin"]), $filters['filtersType']['diseaseOrigin']);
                
            if (isset($filters["years"]))
                $checks->inYears($this->getValuesForMultiselect($filters["years"]), $filters['filtersType']['years']);

            if (isset($filters["nextFollowDays"]))
                $checks->inNextFollowDays($this->getValuesForMultiselect($filters["nextFollowDays"]), $filters['filtersType']['nextFollowDays']);

            if (isset($filters["sveAssociateds"]))
                $checks->inSveAssociateds($this->getValuesForMultiselect($filters["sveAssociateds"]), $filters['filtersType']['sveAssociateds']);

            if (isset($filters["medicalCertificates"]))
                $checks->inMedicalCertificates($this->getValuesForMultiselect($filters["medicalCertificates"]), $filters['filtersType']['medicalCertificates']);

            if (isset($filters["relocatedTypes"]))
                $checks->inRelocatedTypes($this->getValuesForMultiselect($filters["relocatedTypes"]), $filters['filtersType']['relocatedTypes']);

            if (isset($filters["haveRecommendations"]))
                $checks->inHaveRecommendations($this->getValuesForMultiselect($filters["haveRecommendations"]), $filters['filtersType']['haveRecommendations']);

            if (isset($filters["expiratedRecommendations"]))
                $checks->inExpiratedRecommendations($this->getValuesForMultiselect($filters["expiratedRecommendations"]), $filters['filtersType']['expiratedRecommendations']);
                
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $checks->betweenDate($dates);

            if (isset($filters["headquarters"]))
            {
                $checks->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
                ->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);
            }

            if (isset($filters["processes"]))
            {
                $checks->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_employees.employee_process_id')
                ->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            }
            
            if (isset($filters["areas"]))
            {
                $checks->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees.employee_area_id')
                ->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
            }
        }

        return Vuetable::of($checks)
                    ->addColumn('reinstatements-checks-edit', function ($check) {
                        if (!$this->user->hasRole('Rol Visor Reincorporaciones', $this->team))
                            return $check->isOpen();
                    })
                    ->addColumn('viewVisor', function ($check) {
                        if ($this->team == 409 || $this->team == 1 || $this->team == 130)
                        {
                            if ($this->user->hasRole('Rol Visor Reincorporaciones', $this->team) || $this->user->hasRole('Superadmin', $this->team))
                                return true;
                            else
                                return false;
                        }
                        else
                            return false;
                    })
                    ->addColumn('viewVisorRecommendations', function ($check) {
                       if ($this->user->hasRole('Rol Visor Reincorporaciones Recomendaciones', $this->team) || $this->user->hasRole('Superadmin', $this->team))
                            return true;
                        else
                            return false;
                    })
                    ->addColumn('reinstatements-checks-view', function ($check) {
                        if ($this->user->hasRole('Rol Visor Reincorporaciones', $this->team) || $this->user->hasRole('Rol Visor Reincorporaciones Recomendaciones', $this->team))
                            return false;
                        else
                            return true;
                    })
                    ->addColumn('downloadFile', function ($check) {
                        if ($this->user->hasRole('Rol Visor Reincorporaciones', $this->team) || $this->user->hasRole('Rol Visor Reincorporaciones Recomendaciones', $this->team))
                            return false;
                        else
                            return true;
                    })
                    ->addColumn('reinstatements-checks-letter', function ($check) {
                        if ($this->user->hasRole('Rol Visor Reincorporaciones', $this->team) || $this->user->hasRole('Rol Visor Reincorporaciones Recomendaciones', $this->team))
                            return false;
                        else
                            return true;
                    })
                    ->make();
    }

    public function data2(Request $request)
    {
        try
        {
            $checks = Check::join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')->where('sau_reinc_checks.employee_id', '=', $request->employee_id);

            if ($request->check_id)
                $checks->where('sau_reinc_checks.employee_id', '<>', $request->check_id);
                    
            $checks = $checks->get();

            if ($checks->count() > 0)
            {
                return $this->respondHttp200([
                    'data' => true
                ]);
            }
            else
            {
                return $this->respondHttp200([
                    'data' => false
                ]);
            }
        } catch (Exception $e){
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckRequest $request)
    {
        Validator::make($request->all(), [
            "detail" => [
                function ($attribute, $value, $fail) 
                {
                    if ($value && is_string($value))
                    {
                        $exist = strpos($value, '<');

                        if (is_numeric($exist) && $exist >= 0)
                            $fail('El detalle de recomendaciones no puede contener ninguno de los caracteres especiales indicados');
                        else
                        {
                            $exist = strpos($value, '>');

                            if (is_numeric($exist) && $exist >= 0)
                                $fail('El detalle de recomendaciones no puede contener ninguno de los caracteres especiales indicados');
                        }
                    }
                }
            ]
        ])->validate();

        $this->validate($request, CheckManager::getProcessRules($request));

        try
        {
            DB::beginTransaction();

            $formModel = $this->getFormModel('form_check');

            $check = new Check(CheckManager::checkNullAttrs($request, $this->company));
            $check->company_id = $this->company;

            if ($formModel == 'chia' || $formModel == 'enka' || $formModel == 'mitsubishi' || $formModel == 'familia' || $formModel == 'harinera' || $formModel == 'aguas')
            {
                foreach ($request->dxs as $key => $dx) 
                {
                    $index = $key + 1;
                    $cie_name = 'cie10_code_'.$index.'_id';

                    if ($key > 0)
                    {
                        $check['disease_origin_'.$index] = $dx['disease_origin'];
                        $check[$cie_name] = $dx['cie10_code_id'];
                        $check['laterality_'.$index] = $dx['laterality'];
                        $check['disease_origin_recomendations_'.$index] = isset($dx['disease_origin_recomendations']) ? $dx['disease_origin_recomendations'] : NULL;
                    }   
                    else
                    {
                        $check['disease_origin'] = $dx['disease_origin'];
                        $check['cie10_code_id'] = $dx['cie10_code_id'];
                        $check['laterality'] = $dx['laterality'];
                        $check['disease_origin_recomendations'] = isset($dx['disease_origin_recomendations']) ? $dx['disease_origin_recomendations'] : NULL;
                    }    
                }
            }

            if (!$check->save())
                return $this->respondHttp500();

            if (!CheckManager::saveMedicalMonitoring($check, $request->medical_monitorings))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveLaborMonitoring($check, $request->labor_monitorings))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing', $check, $request->new_tracing, $this->user))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborNotes', $check, $request->new_labor_notes, $this->user))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveFiles($check, $request, $this->user)) 
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveTags('App\Models\PreventiveOccupationalMedicine\Reinstatements\TagsReinstatementCondition', $check, 'reinstatement_condition', $request->reinstatement_condition))
            {
                $check->delete();
                return $this->respondHttp500();
            }


        $this->saveLogActivitySystem('Reincorporaciones - Reportes', 'Se creo un reporte para el empleado '. $check->employee->name. '-' .$check->employee->name.' con el diagnostico '. $check->cie10Code->description.' con el ID de caso '.$check->id);

            DB::commit();

        } catch (Exception $e){
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el formulario'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $check = Check::select('sau_reinc_checks.*')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);

            return $this->respondHttp200([
                'data' => $this->getCheckView($check)
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest  $request
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function update(CheckRequest $request, $id)
    {
        //\Log::info($request);
        Validator::make($request->all(), [
            "detail" => [
                function ($attribute, $value, $fail) 
                {
                    if ($value && is_string($value))
                    {
                        $exist = strpos($value, '<');

                        if (is_numeric($exist) && $exist >= 0)
                            $fail('El detalle de recomendaciones no puede contener ninguno de los caracteres especiales indicados');
                        else
                        {
                            $exist = strpos($value, '>');

                            if (is_numeric($exist) && $exist >= 0)
                                $fail('El detalle de recomendaciones no puede contener ninguno de los caracteres especiales indicados');
                        }
                    }
                }
            ]
        ])->validate();
        $check = Check::select('sau_reinc_checks.*')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);
        
        $employee_old = $check->employee->id.'-'.$check->employee->name;

        $this->validate($request, CheckManager::getProcessRules($request));
        
        try
        {
            DB::beginTransaction();

            $check->fill(CheckManager::checkNullAttrs($request, $this->company));

            $formModel = $this->getFormModel('form_check');

            if ($formModel == 'chia' || $formModel == 'enka' || $formModel == 'mitsubishi'  || $formModel == 'familia' || $formModel == 'harinera' || $formModel == 'aguas')
            {
                for ($i=0; $i < 5; $i++) 
                { 
                    if ($i > 0)
                    {
                        $indexC = $i + 1;
                        $cie_name_delete = 'cie10_code_'.$indexC.'_id';
                    
                        $check['disease_origin_'.$indexC] = NULL;
                        $check[$cie_name_delete] = NULL;
                        $check['laterality_'.$indexC] = NULL;
                        $check['disease_origin_recomendations_'.$indexC] = NULL;
                    }   
                    else
                    {
                        $check['disease_origin'] = NULL;
                        $check['cie10_code_id'] = NULL;
                        $check['laterality'] = NULL;
                        $check['disease_origin_recomendations'] = NULL;
                    }    
                    
                }
                foreach ($request->dxs as $key => $dx) 
                {
                    $index = $key + 1;
                    
                    if ($key > 0)
                        $cie_name = 'cie10_code_'.$index.'_id';

                    if ($key > 0)
                    {
                        $check['disease_origin_'.$index] = $dx['disease_origin'];
                        $check[$cie_name] = $dx['cie10_code_id'];
                        $check['laterality_'.$index] = $dx['laterality'];
                        $check['disease_origin_recomendations_'.$index] = isset($dx['disease_origin_recomendations']) ? $dx['disease_origin_recomendations'] : NULL;
                    }   
                    else
                    {
                        $check['disease_origin'] = $dx['disease_origin'];
                        $check['cie10_code_id'] = $dx['cie10_code_id'];
                        $check['laterality'] = $dx['laterality'];
                        $check['disease_origin_recomendations'] = isset($dx['disease_origin_recomendations']) ? $dx['disease_origin_recomendations'] : NULL;
                    }    
                }
            }
            /*else if ($formModel == 'mitsubishi')
            {
                $check['cie10_code_2_id'] = $request['cie10_code_2_id'];
                $check['laterality_2'] = $request['laterality_2'];
                $check['cie10_code_3_id'] = $request['cie10_code_3_id'];
                $check['laterality_3'] = $request['laterality_3'];
            }*/
            
            if (!$check->save())
                return $this->respondHttp500();

            $employee_new = Employee::find($request->employee_id);            
            $employee_new = $employee_new->id.'-'.$employee_new->name;

            if (!CheckManager::saveMedicalMonitoring($check, $request->medical_monitorings, true))
                return $this->respondHttp500();

            if (!CheckManager::saveLaborMonitoring($check, $request->labor_monitorings, true))
                return $this->respondHttp500();
                
            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing', $check, $request->new_tracing, $this->user, $request->oldTracings))
                return $this->respondHttp500();

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborNotes', $check, $request->new_labor_notes, $this->user, $request->oldLaborNotes))
                return $this->respondHttp500();

            if (!CheckManager::saveFiles($check, $request, $this->user))
                return $this->respondHttp500();

            if (!CheckManager::saveTags('App\Models\PreventiveOccupationalMedicine\Reinstatements\TagsReinstatementCondition', $check, 'reinstatement_condition', $request->reinstatement_condition))
                return $this->respondHttp500();

            CheckManager::deleteData($check, $request->get('delete'));
            
            $this->saveLogActivitySystem('Reincorporaciones - Reportes', 'Se edito el reporte realizado al empleado '. $employee_old.' con el diagnostico '. $check->cie10Code->description . '. Con el id de caso '.$id.'. Pertenecia al empleado '.$employee_new);

            DB::commit();

        } catch (Exception $e){
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el formulario'
        ]);       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $check = Check::select('sau_reinc_checks.*')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);
        
        $this->saveLogActivitySystem('Reincorporaciones - Reportes', 'Se elimino el reporte realizado al empleado '.$check->employee->id.'-'.$check->employee->name.' con el diagnostico '. $check->cie10Code->description . 'Con el id de caso '.$id);

        if (!$check->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino el formulario'
        ]);
    }

    /**
     * this method must be used ONLY for the show and edit method
     * of this class.
     *
     * this method builds all the parameters that an instance of check
     * must have in order to work the form in the view
     *
     * and the return the form view specifying the parameter "isShow"
     * according to the show or edit view
     * 
     * @param  boolean $isShow
     * @return \Illuminate\Http\Response
     */
    private function getCheckView(Check $check)
    {
        $authUser = $this->user;

        $check->load([
            'employee',
            'cie10Code',
            'medicalMonitorings',
            'laborMonitorings',
            'tracings' => function ($query) use ($authUser) {
                
                /*if (!$authUser->checkRoleDefined('Superadmin') && $authUser->can('reinc_checks_view_last_tracing')) {
                    $query->with('madeBy')->orderBy('created_at', 'desc')->limit(1);
                }
                else {
                    $query->with('madeBy')->orderBy('created_at', 'desc');
                }*/
                
                $query->with('madeBy')->orderBy('created_at', 'desc');
            },
            'laborNotes' => function ($query) {
                $query->with('madeBy')->where('role_visor', 'NO')->orderBy('created_at', 'desc');
            },
            'laborNotesRelations' => function ($query) {
                $query->with('madeBy')->where('role_visor', 'SI')->orderBy('created_at', 'desc');
            },
        ]);

        $check->start_recommendations = $this->formatDateToForm($check->start_recommendations);
        $check->end_recommendations = $this->formatDateToForm($check->end_recommendations);
        $check->monitoring_recommendations = $this->formatDateToForm($check->monitoring_recommendations);
        $check->process_origin_done_date = $this->formatDateToForm($check->process_origin_done_date);
        $check->process_pcl_done_date = $this->formatDateToForm($check->process_pcl_done_date);
        $check->date_controversy_origin_1 = $this->formatDateToForm($check->date_controversy_origin_1);
        $check->date_controversy_origin_2 = $this->formatDateToForm($check->date_controversy_origin_2);
        $check->date_controversy_pcl_1 = $this->formatDateToForm($check->date_controversy_pcl_1);
        $check->date_controversy_pcl_2 = $this->formatDateToForm($check->date_controversy_pcl_2);
        $check->relocated_date = $this->formatDateToForm($check->relocated_date);
        $check->start_restrictions = $this->formatDateToForm($check->start_restrictions);
        $check->end_restrictions = $this->formatDateToForm($check->end_restrictions);
        $check->incapacitated_last_extension = $this->formatDateToForm($check->incapacitated_last_extension);
        $check->start_incapacitated = $this->formatDateToForm($check->start_incapacitated);
        $check->end_incapacitated = $this->formatDateToForm($check->end_incapacitated);
        $check->deadline = $this->formatDateToForm($check->deadline);
        $check->next_date_tracking = $this->formatDateToForm($check->next_date_tracking);

        $check->old_process_origin_file = $check->process_origin_file;
        $check->old_process_pcl_file = $check->process_pcl_file;

        $check->multiselect_employee = $check->employee->multiselect();
        $check->multiselect_cie10Code = $check->cie10Code->multiselect();
        $check->multiselect_cie10Code2 = $check->cie10_code_2_id ? $check->cie10Code2->multiselect() : NULL;
        $check->multiselect_cie10Code3 = $check->cie10_code_3_id ? $check->cie10Code3->multiselect() : NULL;
        $check->multiselect_cie10Code4 = $check->cie10_code_4_id ? $check->cie10Code4->multiselect() : NULL;
        $check->multiselect_cie10Code5 = $check->cie10_code_5_id ? $check->cie10Code5->multiselect() : NULL;
        $check->multiselect_restriction = $check->restriction ? $check->restriction->multiselect() : [];
        $check->relocated_regional_multiselect = $check->relocatedRegional ? $check->relocatedRegional->multiselect() : [];
        $check->relocated_headquarter_multiselect = $check->relocatedHeadquarter ? $check->relocatedHeadquarter->multiselect() : [];
        $check->relocated_process_multiselect = $check->relocatedProcess ? $check->relocatedProcess->multiselect() : [];
        $check->relocated_position_multiselect = $check->relocatedPosition ? $check->relocatedPosition->multiselect() : [];

        $check->medicalMonitorings->transform(function($medicalMonitoring, $index) {
            $medicalMonitoring->set_at = $this->formatDateToForm($medicalMonitoring->set_at);
            return $medicalMonitoring;
        });

        $check->laborMonitorings->transform(function($laborMonitoring, $index) {
            $laborMonitoring->set_at = $this->formatDateToForm($laborMonitoring->set_at);
            return $laborMonitoring;
        });

        $check->new_tracing = '';

        $oldTracings = [];

        foreach ($check->tracings as $tracing)
        {
            array_push($oldTracings, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy ? $tracing->madeBy->name :'',
                'updated_at' => $tracing->updated_at->toDateString(),
                'informant_role' => $tracing->informant_role
            ]);
        }

        $check->new_labor_notes = '';
        $check->new_labor_notes_relations = '';

        $check->oldTracings = $oldTracings;

        $oldLaborNotes = [];

        foreach ($check->laborNotes as $tracing)
        {
            array_push($oldLaborNotes, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy ? $tracing->madeBy->name : '',
                'updated_at' => $tracing->updated_at->toDateString()
            ]);
        }

        $check->oldLaborNotes = $oldLaborNotes;

        $oldLaborNotesRelations = [];

        foreach ($check->laborNotesRelations as $tracing)
        {
            array_push($oldLaborNotesRelations, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy ? $tracing->madeBy->name : '',
                'updated_at' => $tracing->updated_at->toDateString()
            ]);
        }

        $check->oldLaborNotesRelations = $oldLaborNotesRelations;

        $files = $check->files;
                    
        $files->transform(function($file, $indexFile) {
            $file->key = Carbon::now()->timestamp + rand(1,10000);
            $file->old_name = $file->file;

            return $file;
        });

        $check->files = $files;

        $check->delete = [
            'files' => []
        ];

        $formModel = $this->getFormModel('form_check');

        if ($formModel == 'chia' || $formModel == 'enka' || $formModel == 'mitsubishi' || $formModel == 'familia' || $formModel == 'harinera' || $formModel == 'aguas')
        {
            $dxs = [];

            if ($check->disease_origin)
            {
                $content = [
                    'key' => Carbon::now()->timestamp + rand(1,10000),
                    'disease_origin' => $check->disease_origin,
                    'cie10_code_id' => $check->cie10_code_id,
                    'multiselect_cie10Code' => $check->multiselect_cie10Code,
                    'laterality' => $check->laterality,
                    'disease_origin_recomendations' => $check->disease_origin_recomendations
                ];

                array_push($dxs, $content);
            }

            if ($check->disease_origin_2)
            {
                $content = [
                    'key' => Carbon::now()->timestamp + rand(1,10000),
                    'disease_origin' => $check->disease_origin_2,
                    'cie10_code_id' => $check->cie10_code_2_id,
                    'multiselect_cie10Code' => $check->multiselect_cie10Code2,
                    'laterality' => $check->laterality_2,
                    'disease_origin_recomendations' => $check->disease_origin_recomendations_2
                ];

                array_push($dxs, $content);
            }

            if ($check->disease_origin_3)
            {
                $content = [
                    'key' => Carbon::now()->timestamp + rand(1,10000),
                    'disease_origin' => $check->disease_origin_3,
                    'cie10_code_id' => $check->cie10_code_3_id,
                    'multiselect_cie10Code' => $check->multiselect_cie10Code3,
                    'laterality' => $check->laterality_3,
                    'disease_origin_recomendations' => $check->disease_origin_recomendations_3
                ];

                array_push($dxs, $content);
            }

            if ($check->disease_origin_4)
            {
                $content = [
                    'key' => Carbon::now()->timestamp + rand(1,10000),
                    'disease_origin' => $check->disease_origin_4,
                    'cie10_code_id' => $check->cie10_code_4_id,
                    'multiselect_cie10Code' => $check->multiselect_cie10Code4,
                    'laterality' => $check->laterality_4,
                    'disease_origin_recomendations' => $check->disease_origin_recomendations_4
                ];

                array_push($dxs, $content);
            }
            
            if ($check->disease_origin_5)
            {
                $content = [
                    'key' => Carbon::now()->timestamp + rand(1,10000),
                    'disease_origin' => $check->disease_origin_5,
                    'cie10_code_id' => $check->cie10_code_5_id,
                    'multiselect_cie10Code' => $check->multiselect_cie10Code5,
                    'laterality' => $check->laterality_5,
                    'disease_origin_recomendations' => $check->disease_origin_recomendations_5
                ];

                array_push($dxs, $content);
            }

            $check->dxs = $dxs;
        }

        return $check;
    }

    public function downloadOriginFile($id)
    {
        $check = Check::select(
                    'sau_reinc_checks.*',
                    'sau_employees.*'
                )
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);

        return Storage::disk('public')->download('preventiveOccupationalMedicine/reinstatements/files/'.$this->company.'/'.$check->process_origin_file, 'Calificacion de origen '.$check->identification.'.pdf');
    }

    public function downloadPclFile($id)
    {
        $check = Check::select(
                    'sau_reinc_checks.*',
                    'sau_employees.*'
                )
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);

        return Storage::disk('public')->download('preventiveOccupationalMedicine/reinstatements/files/'.$this->company.'/'.$check->process_pcl_file, 'Proceso PCL '.$check->identification.'.pdf');
    }

    public function downloadFile(CheckFile $file)
    {
        $check = Check::select(
                    'sau_reinc_checks.*',
                    'sau_employees.*'
                )
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($file->check_id);

        $directory = "preventiveOccupationalMedicine/reinstatements/files/{$check->company_id}/{$file->file}";

        $name = $file->file_name;

        if ($name)
            return Storage::disk('s3')->download($directory, $name);
        else
            return Storage::disk('s3')->download($directory);
    }

    public function generateTracing(Request $request)
    {
        $check = Check::selectRaw(
           'sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_employees_positions.name AS position,
            sau_reinc_cie10_codes.description AS diagnosis,
            sau_reinc_checks.disease_origin AS disease_origin,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            sau_reinc_checks.end_recommendations AS end_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_reinc_checks.monitoring_recommendations AS monitoring_recommendations,
            sau_employees_headquarters.name AS headquarter,
            sau_reinc_checks.origin_recommendations as origin_recommendations'
        )
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $request->check_id)
        ->first();

        $now = Carbon::now();

        $check->income_date = Carbon::parse($check->income_date, 'UTC')->isoFormat('ll');

        $check->now = Carbon::parse($now, 'UTC')->isoFormat('ll');

        $company = Company::select('logo')->where('id', $this->company)->first();
        $firm_path = [
            'firm' => $this->user->firm ? Storage::disk('s3')->url('administrative/firms_users/'. $this->user->id . '/' . $this->user->firm) : null,
            'name' => $this->user->name
        ];
        $new_date_tracing = $request->new_date_tracing ? (Carbon::createFromFormat('D M d Y', $request->new_date_tracing))->format('Y-m-d') : '';

        $data = [
            'has_tracing' => $request->has_tracing,
            'new_date_tracing' => $new_date_tracing,
            'tracing_description' => $request->tracing_description,
            'check' => $check,
            'logo' => ($company && $company->logo) ? $company->logo : null,
            'firm' =>  $firm_path
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('reinc_letter_tracing');

        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.tracing', $data);
        }
        else if ($formModel == 'familia')
        {
            $pdf = PDF::loadView('pdf.tracingFamilia', $data);
        }
        else if ($formModel == 'chia')
        {
            $pdf = PDF::loadView('pdf.tracingChia', $data);
        }

        return $pdf->stream('seguimiento.pdf');
    }

    public function generateTracingGlobal(Request $request)
    {
        $check = Check::selectRaw(
           'sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_employees_positions.name AS position,
            sau_reinc_cie10_codes.description AS diagnosis,
            sau_reinc_checks.disease_origin AS disease_origin,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            sau_reinc_checks.end_recommendations AS end_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_reinc_checks.monitoring_recommendations AS monitoring_recommendations,
            sau_employees_headquarters.name AS headquarter,
            sau_reinc_checks.origin_recommendations as origin_recommendations'
        )
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $request->check_id)
        ->first();

        $tracings = Tracing::where('check_id', $request->check_id)->get();

        $now = Carbon::now();

        $check->income_date = Carbon::parse($check->income_date, 'UTC')->isoFormat('ll');

        $check->now = Carbon::parse($now, 'UTC')->isoFormat('ll');

        $company = Company::select('logo')->where('id', $this->company)->first();

        $firm_path = [
            'firm' => $this->user->firm ? Storage::disk('s3')->url('administrative/firms_users/'. $this->user->id . '/' . $this->user->firm) : null,
            'name' => $this->user->name
        ];

        $data = [
            'tracings' => $tracings,
            'check' => $check,
            'logo' => ($company && $company->logo) ? $company->logo : null,
            'firm' =>  $firm_path
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('reinc_letter_tracing');

        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.tracingGlobal', $data);
        }
        else if ($formModel == 'familia')
        {
            $pdf = PDF::loadView('pdf.tracingFamiliaGlobal', $data);
        }
        else if ($formModel == 'chia')
        {
            $pdf = PDF::loadView('pdf.tracingGlobalChia', $data);
        }

        return $pdf->stream('seguimientos.pdf');
    }

    public function generateLetter(Request $request)
    {
        $check = Check::selectRaw(
           'sau_reinc_checks.detail as check_detail,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            sau_reinc_checks.disease_origin AS disease_origin,
            sau_reinc_checks.Observations_recommendatios AS Observations_recommendatios,
            sau_employees.income_date AS income_date,
            sau_reinc_checks.end_recommendations AS end_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name AS headquarter,
            sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_reinc_checks.origin_recommendations as origin_recommendations,
            sau_employees_positions.name AS position,
            sau_reinc_checks.position_functions_assigned_reassigned as position_functions_assigned_reassigned'
        )
        ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', '=', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', '=', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', '=', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $request->check_id)
        ->first();

        $now = Carbon::now();

        $check->income_date = Carbon::parse($check->income_date, 'UTC')->isoFormat('ll');

        $date = Carbon::parse($now, 'UTC')->isoFormat('ll');
            
        $company = Company::select('logo')->where('id', $this->company)->first();

        $record_letter = LetterHistory::where('check_id', $request->check_id)->where('to', $request->to)->where('from', $request->from)->where('subject', $request->subject)->where('user_id', $this->user->id)->first();

        if (!$record_letter)
        {
            $record_letter = new LetterHistory;
            $record_letter->to = $request->to;
            $record_letter->from = $request->from;
            $record_letter->subject = $request->subject;
            $record_letter->send_date = $date;
            $record_letter->check_id = $request->check_id;
            $record_letter->company_id = $this->company;
            $record_letter->user_id = $this->user->id;
            $record_letter->save();
            $record_letter->user_name = $record_letter->user->name;
        }

        $data = [
            'to' => $request->to,
            'from' => $request->from,
            'subject' => $request->subject, 
            'history_letter' => $record_letter,
            'user' => $this->user,
            'check' => $check,
            'date' => $date,
            'income_date' => $check->income_date,
            'firm' => $request->firm,
            'firm_user' => $this->user->firm ? Storage::disk('s3')->url('administrative/firms_users/'. $this->user->id . '/' . $this->user->firm) : null,
            'recommendations' => $this->replaceLast(',', ' y ', $request->selectedRecommendations),
            'observations_recommendatiosn' => $check->Observations_recommendatios,
            'logo' => ($company && $company->logo) ? $company->logo : null
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('reinc_letter_recomendatios');
        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.letter', $data);
        }
        else if ($formModel == 'vivaAir')
        {
            $pdf = PDF::loadView('pdf.letterVivaAir', $data);
        }
        else if ($formModel == 'hptu')
        {
            $pdf = PDF::loadView('pdf.letterHptu', $data);
        }
        else if($formModel == 'reditos')
        {
            $pdf = PDF::loadView('pdf.letterReditos', $data);
        }
        else if($formModel == 'manpower')
        {
            $pdf = PDF::loadView('pdf.letterManpower', $data);
        }
        else if($formModel == 'familia')
        {
            $pdf = PDF::loadView('pdf.letterFamilia', $data);
        }
        else if($formModel == 'chia')
        {
            $pdf = PDF::loadView('pdf.letterChia', $data);
        } 

        return $pdf->stream('recomendaciones.pdf');
    
    }

    private function replaceLast($buscar, $remplazar, $texto)
    {
        $pos = strrpos($texto, $buscar);
        
        if($pos !== false)
            $texto = substr_replace($texto, $remplazar, $pos, strlen($buscar));
        
        return $texto;
    }

    public function multiselectDiseaseOrigin(Request $request)
    {
        return $this->getSelectOptions("reinc_select_disease_origin");
    }

    public function multiselectSveAssociateds(Request $request)
    {
        return $this->getSelectOptions("reinc_select_sve_associated");
    }

    public function multiselectMedicalCertificates(Request $request)
    {
        return $this->getSelectOptions("reinc_select_medical_certificate_ueac");
    }

    public function multiselectRelocatedTypes(Request $request)
    {
        return $this->getSelectOptions("reinc_select_relocated_types");
    }

    /**
     * returns the days remaining for the next follow-up of the reports
     * @return collection
     */
    public function multiselectNextFollowDays(Request $request)
    {
        $checks = Check::selectRaw('next_date_tracking, DATEDIFF(next_date_tracking, CURDATE()) as day')
                    ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
                    ->whereRaw('next_date_tracking >= CURDATE()')
                    ->groupBy('next_date_tracking')
                    ->orderBy('day');
        
        $data = $checks->pluck('next_date_tracking', 'day')->toArray();
        $data['Vencidas'] = 'Vencidas';
        $data['No Aplica'] = 'No Aplica';

        return $this->multiSelectFormat(collect($data));
    }

    /**
     * returns the days remaining for the next follow-up of the reports
     * @return collection
     */
    public function multiselectYears()
    {
        $checks = Check::selectRaw(
            'DISTINCT YEAR(sau_reinc_checks.created_at) AS year'
        )
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->orderBy('year')
        ->get()
        ->pluck('year', 'year');

      return $this->multiSelectFormat($checks);
    }

    /**
     * toggles the check state between open and close
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function toggleState(Request $request, $id)
    {
        $check = Check::select('sau_reinc_checks.*')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);

        $newState = $check->isOpen() ? "CERRADO" : "ABIERTO";

        if ($check->isOpen() && isset($request->motive_close) && $request->motive_close)  
        {      
            foreach ($request->motive_close as $key => $value)
            {
                $data2['motive_close'][$key] = json_decode($value, true);
                $request->merge($data2);
            }
            //$motives = json_decode($request->get('motive_close'), true);
            $motive = $this->tagsPrepare($request->get('motive_close'));
            $this->tagsSave($motive, TagsMotiveClose::class);

            $data = [
                'state' => $newState, 
                'motive_close' => $motive ? $motive->implode(',') : NULL
            ];
        }
        else
        {
            $data = [
                'state' => $newState,
                'motive_close' => NULL
            ];
        }


        if ($request->has('deadline') && $check->isOpen())
            $data['deadline'] = $this->formatDateToSave($request->get('deadline'));
        else 
            $data['deadline'] = null;

        if (!$check->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del reporte'
        ]);
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
      try
      {
        $identifications = $this->getValuesForMultiselect($request->identifications);
        $names = $this->getValuesForMultiselect($request->names);
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $businesses = $this->getValuesForMultiselect($request->businesses);
        $diseaseOrigin = $this->getValuesForMultiselect($request->diseaseOrigin);
        $years = $this->getValuesForMultiselect($request->years);
        $nextFollowDays = $request->has('nextFollowDays') ? $this->getValuesForMultiselect($request->nextFollowDays) : null;
        $sveAssociateds = $request->has('sveAssociateds') ? $this->getValuesForMultiselect($request->sveAssociateds) : null;
        $medicalCertificates = $request->has('medicalCertificates') ? $this->getValuesForMultiselect($request->medicalCertificates) : null;
        $relocatedTypes = $request->has('relocatedTypes') ? $this->getValuesForMultiselect($request->relocatedTypes) : null;
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }

        $filters = [
            'identifications' => $identifications,
            'names' => $names,
            'regionals' => $regionals,
            'businesses' => $businesses,
            'diseaseOrigin' => $diseaseOrigin,
            'years' => $years,
            'nextFollowDays' => $nextFollowDays,
            'sveAssociateds' => $sveAssociateds,
            'medicalCertificates' => $medicalCertificates,
            'relocatedTypes' => $relocatedTypes,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        CheckExportJob::dispatch($this->user, $this->company, $filters);
      
        return $this->respondHttp200();
      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }

    public function tracingOthers(Request $request)
    {
        $tracingOthers = CheckManager::getTracingOthers($request->employee_id, $request->check_id, $request->table);

        return $this->respondHttp200([
            'data' => $tracingOthers
        ]);
    }

    public function downloadPdf($id)
    {
        $check = Check::select('sau_reinc_checks.*')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->findOrFail($id);

        $checks = $this->getCheckView($check);

        $checks->employee->antiquity = $this->timeDifference($checks->employee->income_date);
        $checks->employee->age = $checks->employee->age ? $checks->employee->age : ($checks->employee->date_of_birth ? $this->timeDifference((Carbon::createFromFormat('Y-m-d',$checks->employee->date_of_birth))->toDateString()) : '');
        //$checks->employee->age = $checks->employee->date_of_birth ? $this->timeDifference((Carbon::createFromFormat('Y-m-d',$checks->employee->date_of_birth))->toDateString()) : '';

        //\Log::info($checks);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('form_check');

        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.reporteReinstatements', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()]);
        }
        else if ($formModel == 'misionEmpresarial')
        {
            $pdf = PDF::loadView('pdf.reportReinstatementsMisionEmpresarial', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()]);
        }
        else if ($formModel == 'hptu')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsHptu', ['check' => $check, 'locationForm' => $this->getLocationFormConfModule()]);
        }
        else if($formModel == 'vivaAir')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsVivaAir', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()]);
        }
        else if($formModel == 'argos')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsArgos', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'manpower')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsManPower', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'ingeomega')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsIngeomega', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'familia')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsFamilia', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'harinera')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsHarinera', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'chia')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsChia', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if($formModel == 'mitsubishi')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsMitsubishi', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }
        else if ($formModel == 'enka')
        {
            $pdf = PDF::loadView('pdf.reporteReinstatementsEnka', ['check' => $checks, 'locationForm' => $this->getLocationFormConfModule()] );
        }

        $pdf->setPaper('A3', 'landscape');

        return $pdf->download('reporte.pdf');
    }

    private function timeDifference($startDate, $endDate = null)
    {
        $start = new DateTime($startDate);
        $end;

        if ($endDate == null)
            $end = new DateTime();
        else
            $start = new DateTime($endDate);

        $interval = $start->diff($end);

        return $interval->format('%y años %m meses y %d dias');
    }

    public function multiselectReinstatementcondition(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsReinstatementCondition::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectInformantRole(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsInformantRole::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectMotiveClose(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsMotiveClose::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function dataLetters(Request $request)
    {
        $headquarters =$this->user->headquarters()->pluck('id')->toArray();

        $data = LetterHistory::select(
            'sau_reinc_letter_recommendations_history.*',
            'sau_reinc_cie10_codes.code AS code',
            'sau_reinc_checks.disease_origin AS disease_origin',
            'sau_employees.name AS name'
        )
        ->join('sau_reinc_checks', 'sau_reinc_checks.id', 'sau_reinc_letter_recommendations_history.check_id')        
        ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id');

        if (count($headquarters) > 0)
        {
            $headquarters = implode(',', $headquarters);

            $data->whereRaw("sau_employees.employee_headquarter_id in ({$headquarters})");
        }

        return Vuetable::of($data)
            ->make();
    }

    public function regenerateLetter($id)
    {
        $record_letter = LetterHistory::find($id);

        $check = Check::selectRaw(
           'sau_reinc_checks.detail as check_detail,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            sau_reinc_checks.disease_origin AS disease_origin,
            sau_reinc_checks.Observations_recommendatios AS Observations_recommendatios,
            sau_employees.income_date AS income_date,
            sau_reinc_checks.end_recommendations AS end_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name AS headquarter,
            sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_reinc_checks.origin_recommendations as origin_recommendations,
            sau_employees_positions.name AS position,
            sau_reinc_checks.position_functions_assigned_reassigned as position_functions_assigned_reassigned'
        )
        ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', '=', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', '=', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', '=', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $record_letter->check_id)
        ->first();

        $now = Carbon::now();

        $check->income_date = Carbon::parse($check->income_date, 'UTC')->isoFormat('ll');

        $date = $record_letter->send_date;
            
        $company = Company::select('logo')->where('id', $this->company)->first();

        $data = [
            'to' => $record_letter->to,
            'from' => $record_letter->from,
            'subject' => $record_letter->subject, 
            'user' => $record_letter->user ? $record_letter->user : $this->user,
            'check' => $check,
            'firm_user' => $this->user->firm ? Storage::disk('s3')->url('administrative/firms_users/'. $this->user->id . '/' . $this->user->firm) : null,
            'date' => $record_letter->send_date,
            'income_date' => $check->income_date,
            'observations_recommendatiosn' => $check->Observations_recommendatios,
            'logo' => ($company && $company->logo) ? $company->logo : null
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('reinc_letter_recomendatios');
        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.letter', $data);
        }
        else if ($formModel == 'vivaAir')
        {
            $pdf = PDF::loadView('pdf.letterVivaAir', $data);
        }
        else if ($formModel == 'hptu')
        {
            if ($record_letter->not_recommendations)
                $pdf = PDF::loadView('pdf.letterHptuNotRecommendations', $data);
            else
                $pdf = PDF::loadView('pdf.letterHptu', $data);
        }
        else if($formModel == 'reditos')
        {
            $pdf = PDF::loadView('pdf.letterReditos', $data);
        }
        else if($formModel == 'manpower')
        {
            $pdf = PDF::loadView('pdf.letterManpower', $data);
        }
        else if($formModel == 'familia')
        {
            $pdf = PDF::loadView('pdf.letterFamilia', $data);
        }
        else if($formModel == 'chia')
        {
            $pdf = PDF::loadView('pdf.letterChia', $data);
        }

        return $pdf->stream('recomendaciones.pdf');
    
    }

    public function getMessageIncapacitate(Request $request)
    {
        $check = Check::find($request->check_id);
        $message = NULL;

        if ($check->has_incapacitated == 'SI' && $check->start_incapacitated)
        {
            $configDay = $this->getConfig();

            if ($configDay)
            {
                $days = $this->timeDifferenceDays((Carbon::createFromFormat('Y-m-d', $check->start_incapacitated))->toDateString());

                if (COUNT($configDay) == 3)
                {
                    if ($days >= $configDay[2])
                        $message = "El empleado ha superado los ".$configDay[2]." dias de incapacidad";
                    else if ($days >= $configDay[1])
                        $message = "El empleado ha superado los ".$configDay[1]." dias de incapacidad";
                    else if ($days >= $configDay[0])
                        $message = "El empleado ha superado los ".$configDay[0]." dias de incapacidad";
                }
                if (COUNT($configDay) == 2)
                {
                    if ($days >= $configDay[1])
                        $message = "El empleado ha superado los ".$configDay[1]." dias de incapacidad";
                    else if ($days >= $configDay[0])
                        $message = "El empleado ha superado los ".$configDay[0]." dias de incapacidad";
                }
                if (COUNT($configDay) == 1)
                {
                    if ($days >= $configDay[0])
                        $message = "El empleado ha superado los ".$configDay[0]." dias de incapacidad";
                }
            }
        }

        return $message;
    }

    private function timeDifferenceDays($startDate, $endDate = null)
    {
      $start = new DateTime($startDate);
      $end;

      if ($endDate == null)
          $end = new DateTime();
      else
          $start = new DateTime($endDate);

      $interval = $start->diff($end);

      return $interval->format('%a');
    }

    public function getConfig()
    {
        $key = "notify_incapacitated";
        $key1 = "days_notify_incapacitated";
        $key2 = "days_notify_incapacitated_2";
        $key3 = "days_notify_incapacitated_3";
        
        try
        {
            $exists = ConfigurationsCompany::company($this->company)->findByKey($key);

            if ($exists && $exists == 'SI')
            {
                $days = [];

                $days_1 = ConfigurationsCompany::company($this->company)->findByKey($key1);
                
                array_push($days, $days_1);

                $days_2 = ConfigurationsCompany::company($this->company)->findByKey($key2);

                $days_3 = ConfigurationsCompany::company($this->company)->findByKey($key3);

                if ($days_2 && $days_2 > 0)
                    array_push($days, $days_2);

                if ($days_3 && $days_3 > 0)
                    array_push($days, $days_3);
    
                return $days;
            }
            else
                return NULL;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    public function sendEmailRecommendations(Request $request)
    {
        $check = Check::selectRaw(
            'sau_reinc_checks.detail as check_detail,
             sau_reinc_checks.start_recommendations AS start_recommendations,
             sau_reinc_checks.disease_origin AS disease_origin,
             sau_reinc_checks.Observations_recommendatios AS Observations_recommendatios,
             sau_employees.income_date AS income_date,
             sau_reinc_checks.end_recommendations AS end_recommendations,
             DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
             sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
             sau_employees_regionals.name as regional,
             sau_employees_headquarters.name AS headquarter,
             sau_employees.name AS name,
             sau_employees.identification AS identification,
             sau_reinc_checks.origin_recommendations as origin_recommendations,
             sau_employees_positions.name AS position,
             sau_reinc_checks.position_functions_assigned_reassigned as position_functions_assigned_reassigned'
         )
         ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
         ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', '=', 'sau_employees.employee_regional_id')
         ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', '=', 'sau_employees.employee_headquarter_id')
         ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', '=', 'sau_employees.employee_position_id')
         ->where('sau_reinc_checks.id', $request->check_id)
         ->first();
 
         $now = Carbon::now();
 
         $check->income_date = Carbon::parse($check->income_date, 'UTC')->isoFormat('ll');
 
         $date = Carbon::parse($now, 'UTC')->isoFormat('ll');
 
         $data = [
             'to' => $request->email_1.', '.$request->email_2,
             'from' => $this->user->name,
             'subject' => 'Recomendaciones',
             'user' => $this->user,
             'check' => $check,
             'date' => $date,
             'income_date' => $check->income_date,
             'recommendations' => $this->replaceLast(',', ' y ', $request->selectedRecommendations),
             'observations_recommendatiosn' => $check->Observations_recommendatios,
         ];

        $recipients = User::where('id', -1)->get();
        $recipients->push(new User(['email' => $request->email_1]));
        $recipients->push(new User(['email' => $request->email_2]));

        $record_letter = new LetterHistory;
        $record_letter->to = $request->email_1.', '.$request->email_2;
        $record_letter->from = $this->user->name;
        $record_letter->subject = 'Recomendaciones';
        $record_letter->send_date = $date;
        $record_letter->check_id = $request->check_id;
        $record_letter->company_id = $this->company;
        $record_letter->user_id = $this->user->id;

        if ($request->continue_recommendations == 'SI')
        {        
            NotificationMail::
            subject('Reincorporaciones - Recomendaciones')
            ->message("COMUNICACIÓN INTERNA")
            ->recipients($recipients)
            ->module('reinstatements')
            ->view("preventiveoccupationalmedicine.reinstatements.recommendations")
            ->with(['data' => $data])
            ->company($this->company)
            ->send();

            $record_letter->not_recommendations = false;
        }
        else
        {
            NotificationMail::
                subject('Reincorporaciones - Recomendaciones')
                ->message("COMUNICACIÓN INTERNA")
                ->recipients($recipients)
                ->module('reinstatements')
                ->view("preventiveoccupationalmedicine.reinstatements.not_recommendations")
                ->with(['data' => $data])
                ->company($this->company)
                ->send();

            $record_letter->not_recommendations = true;
        }
        
        $record_letter->save();

        return $this->respondHttp200();
    }

    public function saveLaborRelationsNotes(Request $request)
    {
        foreach ($request->new_labor_notes_relations as $key => $value) 
        {
            $note = json_decode($value, true);

            LaborNotes::create(
                [
                    'description' => $note['description'],
                    'check_id' => $request->id,
                    'user_id' => $this->user->id,
                    'role_visor' => 'SI'
                ]
            );
        }
    }

}
