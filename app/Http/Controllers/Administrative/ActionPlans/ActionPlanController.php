<?php

namespace App\Http\Controllers\Administrative\ActionPlans;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\Administrative\ActionPlans\ActionPlansTracing;
use App\Models\Administrative\ActionPlans\ActionPlansFileEvidence;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\Administrative\ActionPlans\ActionPlanRequest;
use App\Jobs\Administrative\ActionPlans\ActionPlanExportJob;
use App\Traits\ContractTrait;
use App\Traits\Filtertrait;
use Illuminate\Support\Facades\Storage;
use DB;

class ActionPlanController extends Controller
{
    use ContractTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        //$this->middleware("permission:actionPlans_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:actionPlans_r, {$this->team}");
        $this->middleware("permission:actionPlans_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:actionPlans_export, {$this->team}", ['only' => 'export']);
        $this->middleware("permission:action_plan_activities_d, {$this->team}", ['only' => 'destroy']);
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
        $url = '/administrative/actionplans';

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if ((isset($filters["regionals"]) && COUNT($filters["regionals"]) > 0) || (isset($filters["headquarters"]) && COUNT($filters["headquarters"]) > 0) || (isset($filters["processes"]) && COUNT($filters["processes"]) > 0) ||(isset($filters["areas"]) && COUNT($filters["areas"]) > 0))
        {
            $regionales = [];
            $headquarters = [];
            $processes = [];
            $areas = [];

            $inspections = ActionPlansActivity::select(
                'sau_action_plans_activities.*',
                'sau_action_plans_activities.state as state_activity',
                'sau_users.name as responsible',
                'sau_modules.display_name',
                'u.name as user_creator')
            ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
            ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
            ->join('sau_users as u', 'u.id', 'sau_action_plans_activities.user_id')
            ->join('sau_ph_inspection_items_qualification_area_location', 'sau_ph_inspection_items_qualification_area_location.item_id', 'sau_action_plans_activity_module.item_id');

            if (isset($filters["regionals"]) && COUNT($filters["regionals"]) > 0)
            {
                $regionales = $this->getValuesForMultiselect($filters["regionals"]);

                if ($filters['filtersType']['regionals'] == 'IN')
                    $inspections->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionales);

                else if ($filters['filtersType']['regionals'] == 'NOT IN')
                    $inspections->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionales);
            }

            if (isset($filters["headquarters"]) && COUNT($filters["headquarters"]) > 0)
            {
                $headquarters = $this->getValuesForMultiselect($filters["headquarters"]);

                if ($filters['filtersType']['headquarters'] == 'IN')
                    $inspections->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters);

                else if ($filters['filtersType']['headquarters'] == 'NOT IN')
                    $inspections->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters);
            }

            if (isset($filters["processes"]) && COUNT($filters["processes"]) > 0)
            {
                $processes = $this->getValuesForMultiselect($filters["processes"]);

                if ($filters['filtersType']['processes'] == 'IN')
                    $inspections->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);

                else if ($filters['filtersType']['processes'] == 'NOT IN')
                    $inspections->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);
            }

            if (isset($filters["areas"]) && COUNT($filters["areas"]) > 0)
            {
                $areas = $this->getValuesForMultiselect($filters["areas"]);

                if ($filters['filtersType']['areas'] == 'IN')
                    $inspections->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);

                else if ($filters['filtersType']['areas'] == 'NOT IN')
                    $inspections->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);
            }


            $report = ActionPlansActivity::select(
                'sau_action_plans_activities.*',
                'sau_action_plans_activities.state as state_activity',
                'sau_users.name as responsible',
                'sau_modules.display_name',
                'u.name as user_creator')
            ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
            ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
            ->join('sau_users as u', 'u.id', 'sau_action_plans_activities.user_id')
            ->join('sau_ph_reports', 'sau_ph_reports.id', 'sau_action_plans_activity_module.item_id');

            /*if ($filters['filtersType']['regionals'] == 'IN')
                $report->whereIn('sau_ph_reports.employee_regional_id', $regionales);

            else if ($filters['filtersType']['regionals'] == 'NOT IN')
                $report->whereNotIn('sau_ph_reports.employee_regional_id', $regionales);*/

            if (isset($filters["regionals"]) && COUNT($filters["regionals"]) > 0)
            {
                $regionales = $this->getValuesForMultiselect($filters["regionals"]);

                if ($filters['filtersType']['regionals'] == 'IN')
                    $report->whereIn('sau_ph_reports.employee_regional_id', $regionales);

                else if ($filters['filtersType']['regionals'] == 'NOT IN')
                    $report->whereNotIn('sau_ph_reports.employee_regional_id', $regionales);
            }

            if (isset($filters["headquarters"]) && COUNT($filters["headquarters"]) > 0)
            {
                $headquarters = $this->getValuesForMultiselect($filters["headquarters"]);

                if ($filters['filtersType']['headquarters'] == 'IN')
                    $report->whereIn('sau_ph_reports.employee_headquarter_id', $headquarters);

                else if ($filters['filtersType']['headquarters'] == 'NOT IN')
                    $report->whereNotIn('sau_ph_reports.employee_headquarter_id', $headquarters);
            }

            if (isset($filters["processes"]) && COUNT($filters["processes"]) > 0)
            {
                $processes = $this->getValuesForMultiselect($filters["processes"]);

                if ($filters['filtersType']['processes'] == 'IN')
                    $report->whereIn('sau_ph_reports.employee_process_id', $processes);

                else if ($filters['filtersType']['processes'] == 'NOT IN')
                    $report->whereNotIn('sau_ph_reports.employee_process_id', $processes);
            }

            if (isset($filters["areas"]) && COUNT($filters["areas"]) > 0)
            {
                $areas = $this->getValuesForMultiselect($filters["areas"]);

                if ($filters['filtersType']['areas'] == 'IN')
                    $report->whereIn('sau_ph_reports.employee_area_id', $areas);

                else if ($filters['filtersType']['areas'] == 'NOT IN')
                    $report->whereNotIn('sau_ph_reports.employee_area_id', $areas);
            }

            $activities = $inspections->union($report);
        }
        else
        {
            $activities = ActionPlansActivity::select(
                'sau_action_plans_activities.*',
                'sau_action_plans_activities.state as state_activity',
                'sau_users.name as responsible',
                'sau_modules.display_name',
                'u.name as user_creator')
            ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
            ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
            ->join('sau_users as u', 'u.id', 'sau_action_plans_activities.user_id');
        }

        if (isset($filters["responsibles"]))
            $activities->inResponsibles($this->getValuesForMultiselect($filters["responsibles"]), $filters['filtersType']['responsibles']);

        if (isset($filters["creators"]))
            $activities->inUsers($this->getValuesForMultiselect($filters["creators"]), $filters['filtersType']['creators']);

        if (isset($filters["modules"]))
            $activities->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);

        if (isset($filters["states"]))
            $activities->inStates($this->getValuesForMultiselect($filters["states"]), $filters['filtersType']['states']);

        if (isset($filters["dateRange"]))
        {
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $activities->betweenDate($dates);
        }
            
        if (!$this->user->hasRole('Superadmin', $this->team))
        {
            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $contract = $this->getContractUser($this->user->id);
                $users = $this->getUsersContract($contract->id);
                $users_list = [$this->user->id];

                foreach ($users as $user)
                {
                    array_push($users_list, $user->id);
                }

                $activities->where(function ($subquery) use ($users_list) {
                    $subquery->whereIn('sau_action_plans_activities.responsible_id', $users_list);
                });
            }
            else
            {
                $activities->where(function ($subquery) {
                    $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->getIdsModulePermissions());

                    $subquery->orWhere('sau_action_plans_activities.responsible_id', $this->user->id);
                });
            }
        }

        return Vuetable::of($activities)
                    ->make();
    }

     /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            return $this->respondHttp200([
                'data' => ActionPlan::prepareActivityDataComponent($id)
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\Administrative\ActionPlans\ActionPlanRequest $request
     * @param ActionPlansActivity $actionplan
     * @return \Illuminate\Http\Response
     */
    public function update(ActionPlanRequest $request, ActionPlansActivity $actionplan)
    {
        try
        {
            DB::beginTransaction();

            ActionPlan::
                    user($this->user)
                ->module('actionPlans')
                ->url(url('/'))
                ->model($actionplan)
                ->activities($request->get('actionPlan'))
                ->save()
                ->sendMail();

            DB::commit();

        } catch(Exception $e){
            DB::rollback();
            $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la actividad'
        ]);
    }

    public function actionPlanModules(Request $request)
    {
        $modules = ActionPlansActivity::selectRaw(
                "GROUP_CONCAT(sau_modules.id) as ids,
                 sau_modules.display_name as name")
            ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id');

        if (!$this->user->hasRole('Superadmin', $this->team))
        {
            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $contract = $this->getContractUser($this->user->id);
                $users = $this->getUsersContract($contract->id);
                $users_list = [$this->user->id];

                foreach ($users as $user)
                {
                    array_push($users_list, $user->id);
                }

                $modules->where(function ($subquery) use ($users_list) {
                    $subquery->whereIn('sau_action_plans_activities.responsible_id', $users_list);
                });
            }
            else
            {
                $modules->where(function ($subquery) {

                    $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->getIdsModulePermissions());

                    $subquery->orWhere('sau_action_plans_activities.responsible_id', $this->user->id);
                });
            }
        }

        $modules = $modules->groupBy('sau_modules.display_name')->pluck('ids', 'name');
        
        return $this->multiSelectFormat($modules);
    }

    /**
     * Export resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try
        {
            $responsibles = $this->getValuesForMultiselect($request->responsibles);
            $modules = $this->getValuesForMultiselect($request->modules);
            $states = $this->getValuesForMultiselect($request->states);

            $filtersType = $request->filtersType;

            $filters = [
                'responsibles' => $responsibles,
                'modules' => $modules,
                'states' => $states,
                'filtersType' => $filtersType
            ];

            $isContract = false;

            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
                $isContract = true;

            ActionPlanExportJob::dispatch($this->user, $this->company, $filters, $this->user->hasRole('Superadmin', $this->team), $this->getIdsModulePermissions(), $isContract);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function multiselectResponsiblesFilter(Request $request)
    {
        $users = User::selectRaw("
                    sau_users.id as id,
                    CONCAT(sau_users.document, ' - ', sau_users.name) as name
                ");

        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                  ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {
            $users->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');
        }

        $users = $users->get();

        $isSuper = $this->user->hasRole('Superadmin', $this->team);

        if (!$isSuper)
        {
            $users = $users->filter(function ($user, $key) {
                return !$user->hasRole('Superadmin', $this->team);
            });
        }

        $users = $users->pluck('id', 'name');

        return $this->multiSelectFormat($users);
    }

     public function destroy($id)
    {
        $activity = ActionPlansActivity::where('id', $id)->first();

        $this->saveLogDelete('Planes de acción', $activity->description);

        if(!$activity->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la actividad'
        ]);
    }

    public function saveTracing(Request $request)
    {
        if ($request->has('delete') && count($request->delete) > 0)
        {
            foreach ($request->delete as $key => $value) 
            {
                $tracingDel = ActionPlansTracing::find($value);

                if ($tracingDel)
                    $tracingDel->delete();
            }
        }

        if ($request->has('tracings') && count($request->tracings) > 0)
        {
            foreach ($request->tracings as $key => $value) 
            {
                $data = json_decode($value, true);

                if (isset($data['id']))
                    $tracing = ActionPlansTracing::find($data['id']);
                else
                {
                    $tracing = new ActionPlansTracing;
                    $tracing->activity_id = $request->activity_id;
                    $tracing->user_id = $this->user->id;
                }
                
                $tracing->tracing = $data['tracing'];
                $tracing->save();
            }
        }

        return $this->respondHttp200([
            'message' => 'Se guardaron los seguimientos'
        ]);
    }

    public function getTracings(Request $request)
    {
        $isEdit = false;

        $tracings = ActionPlansTracing::select(
            'sau_action_plan_activities_tracing.id AS id',
            'sau_action_plan_activities_tracing.tracing As tracing',
            'sau_users.name AS user',
            'sau_action_plan_activities_tracing.created_at AS date'
        )
        ->join('sau_users', 'sau_users.id', 'sau_action_plan_activities_tracing.user_id')
        ->where('activity_id', $request->id)->get();

        if ($tracings->count() > 0)
            $isEdit = true;

        return $this->respondHttp200([
            'delete' => [],
            'tracings' => $tracings,
            'activity_id' => $request->id,
            'isEdit' => $isEdit
        ]);
    }

    public function download(ActionPlansFileEvidence $file)
    {
        $name = $file->file_name;
        return Storage::disk('s3')->download($file->path_donwload(), $name);
    }

    public function report(Request $request)
    {
        $url = '/administrative/actionplans/report';

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        $reports = ActionPlansActivity::selectRaw("
            sau_action_plans_activities.responsible_id as ap_id,
            sau_users.name as name,
            COUNT(sau_action_plans_activities.id) as num_act,
            COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS numero_planes_no_ejecutados,
            COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS numero_planes_ejecutados
        ")
        ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
        ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
        ->groupBy('sau_action_plans_activities.responsible_id');

        if (isset($filters["responsibles"]))
            $reports->inResponsibles($this->getValuesForMultiselect($filters["responsibles"]), $filters['filtersType']['responsibles']);

        if (isset($filters["creators"]))
            $reports->inUsers($this->getValuesForMultiselect($filters["creators"]), $filters['filtersType']['creators']);

        if (isset($filters["modules"]))
            $reports->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);

        if (isset($filters["states"]))
            $reports->inStates($this->getValuesForMultiselect($filters["states"]), $filters['filtersType']['states']);

        if (isset($filters["dateRange"]))
        {
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $reports->betweenDate($dates);
        }

        if (!$this->user->hasRole('Superadmin', $this->team))
        {
            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $contract = $this->getContractUser($this->user->id);
                $users = $this->getUsersContract($contract->id);
                $users_list = [$this->user->id];

                foreach ($users as $user)
                {
                    array_push($users_list, $user->id);
                }

                $reports->where(function ($subquery) use ($users_list) {
                    $subquery->whereIn('sau_action_plans_activities.responsible_id', $users_list);
                });
            }
            else
            {
                $reports->where(function ($subquery) {
                    $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->getIdsModulePermissions());

                    $subquery->orWhere('sau_action_plans_activities.responsible_id', $this->user->id);
                });
            }
        }

        return Vuetable::of($reports)
            ->addColumn('p_num_eje', function ($report) {
                if (($report->numero_planes_ejecutados + $report->numero_planes_no_ejecutados) > 0)
                    $report->p_num_eje = round(($report->numero_planes_ejecutados / ($report->numero_planes_ejecutados + $report->numero_planes_no_ejecutados)) * 100, 1)."%";
                else
                    $report->p_num_eje = '0%';

                return $report->p_num_eje;
            })
            ->addColumn('p_num_no_eje', function ($report) {
                if (($report->numero_planes_ejecutados + $report->numero_planes_no_ejecutados) > 0)
                    $report->p_num_no_eje = round(($report->numero_planes_no_ejecutados / ($report->numero_planes_ejecutados + $report->numero_planes_no_ejecutados)) * 100, 1)."%";
                else
                    $report->p_num_no_eje = '0%';

                return $report->p_num_no_eje;
            })
            ->addColumn('total', function ($report) {

                $report->total = $report->numero_planes_ejecutados + $report->numero_planes_no_ejecutados;

                return $report->total;
            })
            ->make();
    }

    public function reportPie(Request $request)
    {
        $url = '/administrative/actionplans/report';

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        $reports = ActionPlansActivity::selectRaw("
            COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Pendiente', sau_action_plans_activities.id, NULL)) AS Pendientes,
            COUNT(DISTINCT IF(sau_action_plans_activities.state = 'Ejecutada', sau_action_plans_activities.id, NULL)) AS Ejecutados
        ")
        ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
        ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id');

        if (isset($filters["responsibles"]))
            $reports->inResponsibles($this->getValuesForMultiselect($filters["responsibles"]), $filters['filtersType']['responsibles']);

        if (isset($filters["creators"]))
            $reports->inUsers($this->getValuesForMultiselect($filters["creators"]), $filters['filtersType']['creators']);

        if (isset($filters["modules"]))
            $reports->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);

        if (isset($filters["states"]))
            $reports->inStates($this->getValuesForMultiselect($filters["states"]), $filters['filtersType']['states']);

        if (isset($filters["dateRange"]))
        {
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $reports->betweenDate($dates);
        }

        if (!$this->user->hasRole('Superadmin', $this->team))
        {
            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $contract = $this->getContractUser($this->user->id);
                $users = $this->getUsersContract($contract->id);
                $users_list = [$this->user->id];

                foreach ($users as $user)
                {
                    array_push($users_list, $user->id);
                }

                $reports->where(function ($subquery) use ($users_list) {
                    $subquery->whereIn('sau_action_plans_activities.responsible_id', $users_list);
                });
            }
            else
            {
                $reports->where(function ($subquery) {
                    $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->getIdsModulePermissions());

                    $subquery->orWhere('sau_action_plans_activities.responsible_id', $this->user->id);
                });
            }
        }

        $reports = $reports->get();

        $labels = [];
        $data = [];
        $total = 0;

        foreach ($reports as $key => $value) 
        {
            array_push($labels, 'Pendientes');
            array_push($labels, 'Ejecutados');
            array_push($data, ['name' => 'Pendientes', 'value' => $value->Pendientes]);
            array_push($data, ['name' => 'Ejecutados', 'value' => $value->Ejecutados]);
            $total = $value->Ejecutados + $value->Pendientes;
        }

        $data2 = collect([
            'labels' => $labels,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);

        return $data2;

    }
}
