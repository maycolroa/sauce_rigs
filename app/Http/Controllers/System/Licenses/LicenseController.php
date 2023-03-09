<?php

namespace App\Http\Controllers\System\Licenses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\License;
use App\Models\General\ModuleDependence;
use App\Http\Requests\System\Licenses\LicenseRequest;
use App\Jobs\System\Licenses\NotifyLicenseRenewalJob;
use App\Jobs\System\Licenses\ExportLicensesJob;
use Illuminate\Validation\Rule;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use Validator;
use DB;

class LicenseController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:licenses_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:licenses_r, {$this->team}");
        $this->middleware("permission:licenses_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:licenses_d, {$this->team}", ['only' => 'destroy']);
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
        $licenses = License::system()
            ->selectRaw(
                'sau_licenses.*,
                    GROUP_CONCAT(" ", sau_modules.display_name ORDER BY sau_modules.display_name) AS modules,
                    sau_companies.name AS company'
            )
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->where('sau_modules.main', 'SI')
            ->groupBy('sau_licenses.id');

        $url = "/system/licenses";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["modules"]) && $filters["modules"])
                $licenses->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);
                
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $licenses->betweenDate($dates);
        }

        return Vuetable::of($licenses)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\System\Licenses\LicenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LicenseRequest $request)
    {
        Validator::make($request->all(), [
            'add_email' => Rule::requiredIf(function() use($request){

                $company = Company::find($request->company_id);
                $role = Role::defined()->where('name', 'Superadmin')->first();

                $users = User::withoutGlobalScopes()->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->leftJoin('sau_role_user', function($q) use ($company) { 
                    $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                    ->on('sau_role_user.team_id', '=', DB::raw($company->id));
                })
                ->where('sau_role_user.role_id', '<>', $role->id)
                ->groupBy('sau_users.id')
                ->count();

                if ($users == 0)
                    return true;
                else
                    return false;
            })
        ],[
          'required'  => 'Por favor, agregue emails para notificar la creación de la licencia.'
        ])->validate();

        DB::beginTransaction();

        try
        {
            $license = new License($request->all());
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Ymd');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Ymd');
            
            if (!$license->save())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_main, $modules));

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);

            DB::commit();

            $mails = [];

            if ($request->has('add_email'))
                $mails = $this->getDataFromMultiselect($request->get('add_email'));

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Creación');

        } catch(\Exception $e) {
            \Log::info($e);
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la licencia'
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
            $license = License::system()->findOrFail($id);
            $license->multiselect_company = $license->company->multiselect();
            $license->started_at = (Carbon::createFromFormat('Y-m-d', $license->started_at))->format('D M d Y');
            $license->ended_at = (Carbon::createFromFormat('Y-m-d', $license->ended_at))->format('D M d Y');

            $modules = [];

            $mails = [];

            foreach ($license->modules()->main()->get() as $key => $value)
            {               
                array_push($modules, $value->multiselect());
            }

            $license->module_id = $modules;

            $license->add_email = $mails;

            return $this->respondHttp200([
                'data' => $license,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\System\Licenses\LicenseRequest  $request
     * @param  License  $license
     * @return \Illuminate\Http\Response
     */
    public function update(LicenseRequest $request, $id)
    {
        DB::beginTransaction();

        try
        {
            $modificaciones = [];

            $license = License::system()->findOrFail($id);
            $old_company = $license->company_id;
            $old_ended = $license->ended_at;
            $old_started = $license->started_at;

            $license->fill($request->all());
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Y-m-d');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Y-m-d');

            if ($license->started_at != $old_started)
                array_push($modificaciones, ['fecha_inicio' => $license->started_at]);

            if ($license->ended_at != $old_ended)
                array_push($modificaciones, ['fecha_fin' => $license->ended_at]);

            $modulos_old = $license->modules()->where('main', 'SI')->get();

            $modulos_delete = [];

            if ($old_company != $license->company_id || $old_ended != $license->ended_at)
                $license->notified = 'NO';
            
            if (!$license->update())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_main, $modules));

            foreach ($modulos_old as $key => $value) 
            {
                if (!in_array($value->id, $modules_main))
                {
                    array_push($modulos_delete, $value->display_name);
                }
            }

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);
            
            DB::commit();
            
            $mails = $request->has('add_email') ? $this->getDataFromMultiselect($request->get('add_email')) : [];

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Modificación', $modificaciones, $modulos_delete);

        } catch(\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la licencia'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  License  $license
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $license = License::system()->findOrFail($id);
        
        if(!$license->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la licencia'
        ]);
    }

    public function export(Request $request)
    {
        try
        {
          $modules = $this->getValuesForMultiselect($request->modules);
          $filtersType = $request->filtersType;
  
          $dates = [];
          $dates_request = explode('/', $request->dateRange);
  
          if (COUNT($dates_request) == 2)
          {
              array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
              array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
          }
  
          $filters = [
              'modules' => $modules,
              'dates' => $dates,
              'filtersType' => $filtersType
          ];
  
          ExportLicensesJob::dispatch($this->user, $this->company, $filters);
        
          return $this->respondHttp200();
  
        } catch(Exception $e) {
          return $this->respondHttp500();
        }
    }

    public function report(Request $request)
    {
        try
        {
            $headers = [];
            $dates = [];
            $dates_old = [];
            //$dates_request = $request->dateRange ? explode('/', $request->dateRange) : NULL;

            $url = "/system/licenses/report";

            //$filters = $request->dateRange && COUNT($request->dateRange) > 0 ? $request->dateRange : $this->filterDefaultValues($this->user->id, $url);

            $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

            if (COUNT($filters) > 0)
            {                    
                $dates_request = explode('/', $filters["dateRange"]);

                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));

                    array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->subYear(1)->format('Y-m-d'));
                    array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->subYear(1)->format('Y-m-d'));
                }
                else
                {
                    $start = Carbon::now()->startOfYear()->format('Y-m-d');
                    $end = Carbon::now()->format('Y-m-d');
                    $start_old = Carbon::now()->subYear(1)->startOfYear()->format('Y-m-d');
                    $end_old = Carbon::now()->subYear(1)->format('Y-m-d');

                    array_push($dates, $start);
                    array_push($dates, $end);
                    array_push($dates_old, $start_old);
                    array_push($dates_old, $end_old);
                }
            }
            else
            {
                $start = Carbon::now()->startOfYear()->format('Y-m-d');
                $end = Carbon::now()->format('Y-m-d');
                $start_old = Carbon::now()->subYear(1)->startOfYear()->format('Y-m-d');
                $end_old = Carbon::now()->subYear(1)->format('Y-m-d');

                array_push($dates, $start);
                array_push($dates, $end);
                array_push($dates_old, $start_old);
                array_push($dates_old, $end_old);
            }

            $id_license_renew = [];
            $id_module_renew = [];
            $id_module_group_renew = [];
            $id_group_renew = [];
            $table_general = [];
            $table_module = [];
            $table_groups = [];
            $table_groups_modules = [];

            $prueba = License::selectRaw("
                sau_companies.id as company_id,
                sau_licenses.id as license_id,
                sau_modules.display_name as module,
                sau_licenses.started_at as fecha,
                sau_company_groups.name as group_name
            ")
            ->withoutGlobalScopes()
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            //->where('sau_companies.test', DB::raw("'NO'"))
            ->orderBy('sau_licenses.id')
            ->get();

            $companies = $prueba->groupBy('company_id')
            ->each(function($item, $key) use (&$id_license_renew) {
                $i = 0;

                foreach ($item->groupBy('license_id')->all() as $license_id => $license)
                {
                    if ($i > 0)
                        array_push($id_license_renew, $license_id);

                    $i++;
                }
            });

            $modules = $prueba->groupBy('company_id')
            ->each(function($modules, $companyId) use (&$id_module_renew) {
                $modules->groupBy('module')
                ->each(function($licenses, $moduleId) use (&$id_module_renew, $companyId) {
                    $i = 0;

                    foreach ($licenses as $license)
                    {
                        if (!isset($id_module_renew[$moduleId]))
                            $id_module_renew[$moduleId] = [];

                        if ($i > 0)
                            array_push($id_module_renew[$moduleId], $license->license_id);

                        $i++;
                    }
                });
            });

            $grupos_modulos = [];

            $modules = $prueba->groupBy('group_name')
            ->each(function($modules, $companyId) use (&$id_module_group_renew, &$grupos_modulos) {
                $modules->groupBy('module')
                ->each(function($licenses, $moduleId) use (&$id_module_group_renew, $companyId, &$grupos_modulos) {
                    $i = 0;

                    foreach ($licenses as $license)
                    {
                        if ($license->group_name == 'GRIM') 

                        if (!isset($id_module_group_renew[$moduleId]))
                        {
                            $id_module_group_renew[$moduleId] = [];
                        }

                        if (!isset($grupos_modulos[$license->group_name]))
                        {
                            if (!is_null($license->group_name))
                            {
                                $grupos_modulos[$license->group_name] = [];
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }
                        else
                        {
                            if (!is_null($license->group_name))
                            {
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }

                        if ($i > 0)
                        {
                            if (!isset($id_module_group_renew[$moduleId]))
                            {
                                $id_module_group_renew[$moduleId] = [];
                            }
                            else
                                array_push($id_module_group_renew[$moduleId], $license->license_id);
                            if (!is_null($license->group_name))
                            {
                                array_push($grupos_modulos[$license->group_name], $license->module);
                            }
                        }

                        $i++;
                    }
                });
            });

            $prueba = $prueba->map(function ($item, $key) use ($id_license_renew, $id_module_renew, $id_module_group_renew) {
                $item->renewed = in_array($item->license_id, $id_license_renew);
                $item->renewed_module = isset($id_module_renew[$item->module]) && in_array($item->license_id, $id_module_renew[$item->module]);
                $item->renewed_group_module = isset($id_module_group_renew[$item->module]) && in_array($item->license_id, $id_module_group_renew[$item->module]);

                return $item;
            });

            $headers['general'] = [                    
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención']
            ];

            $headers['group'] = [                       
                ['name' => 'group', 'label' => 'Grupo de compañia'],                 
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención']
            ];

            $headers['module'] = [      
                ['name' => 'module', 'label' => 'Módulo'],
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención']
            ];

            $headers['group_module'] = [      
                ['name' => 'group', 'label' => 'Grupo de compañia'],
                ['name' => 'module', 'label' => 'Módulo'],
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención']
            ];


            if (COUNT($dates) > 0)
            {
                $range_actual = $prueba->filter(function ($item, $key) use ($dates) {
                    return Carbon::parse($item->fecha)->between($dates[0], $dates[1]);
                });

                $modules_actual = $range_actual->pluck('module');

                $range_old = $prueba->filter(function ($item, $key) use ($dates_old) {
                    return Carbon::parse($item->fecha)->between($dates_old[0], $dates_old[1]);
                });

                $modules_old = $range_old->pluck('module');

                $modules_all = $modules_actual->merge($modules_old)->unique()->values();

                $groups = $prueba->filter(function ($item, $key) {
                    return $item->group_name;
                })
                ->pluck('group_name')->unique()->values();

                foreach ($groups as $key => $group) {
                    $retention = $range_old->where('group_name', $group)->count() > 0 ? round(($range_actual->where('group_name', $group)->where('renewed', true)->where('renewed_module', true)->count()/$range_old->where('group_name', $group)->count())*100, 2) : 0;

                    $content = [
                        'group' => $group,
                        'renew_old' => $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total_old' => $range_old->where('group_name', $group)->count(),
                        'renew' => $range_actual->where('group_name', $group)->where('renewed_module', true)->where('renewed', true)->count(),
                        'new' => $range_actual->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total' => $range_actual->where('group_name', $group)->count(),
                        'retention' => $retention.'%'
                    ];

                    array_push($table_groups, $content);
                }


                $retention_sg = $range_old->where('group_name', NULL)->count() > 0 ? round(($range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count()/$range_old->where('group_name', NULL)->count())*100, 2) : 0;

                $content = [
                    'group' => 'Sin grupo',
                    'renew_old' => $range_old->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new_old' => $range_old->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total_old' => $range_old->where('group_name', NULL)->count(),
                    'renew' => $range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new' => $range_actual->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total' => $range_actual->where('group_name', NULL)->count(),
                    'retention' => $retention_sg.'%'
                ];

                $retention_sg_t = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->where('renewed_module', true)->count()/$range_old->count())*100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed',false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed',false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_sg_t.'%'
                ];

                array_push($table_groups, $content);
                array_push($table_groups, $content2);

                foreach ($modules_all as $key => $value) 
                {
                    $retention = $range_old->where('module', $value)->count() > 0 ? round(($range_actual->where('module', $value)->where('renewed_module', true)->count()/$range_old->where('module', $value)->count())*100, 2) : 0;

                    $content = [
                        'module' => $value,
                        'renew_old' => $range_old->where('module', $value)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('module', $value)->where('renewed_module',false)->count(),
                        'total_old' => $range_old->where('module', $value)->count(),
                        'renew' => $range_actual->where('module', $value)->where('renewed_module', true)->count(),
                        'new' => $range_actual->where('module', $value)->where('renewed_module',false)->count(),
                        'total' => $range_actual->where('module', $value)->count(),
                        'retention' => $retention.'%'
                    ];

                    array_push($table_module, $content);
                }


                $retention_m = $range_old->unique('license_id')->count() > 0 ? round(($range_actual->where('renewed_module', true)->unique('license_id')->count()/$range_old->unique('license_id')->count())*100, 2) : 0;

                $content2 = [
                    'module' => 'Total',
                    'renew_old' => $range_old->where('renewed_module', true)->unique('license_id')->count(),
                    'new_old' => $range_old->where('renewed_module',false)->unique('license_id')->count(),
                    'total_old' => $range_old->unique('license_id')->count(),
                    'renew' => $range_actual->where('renewed_module', true)->unique('license_id')->count(),
                    'new' => $range_actual->where('renewed_module',false)->unique('license_id')->count(),
                    'total' => $range_actual->unique('license_id')->count(),
                    'retention' => $retention_m.'%'
                ];

                array_push($table_module, $content2);

                foreach ($groups as $key => $group) 
                {
                    foreach (collect($grupos_modulos[$group])->unique()->values() as $key => $value) 
                    {
                        $retention = $range_old->where('group_name', $group)->where('module', $value)->count() > 0 ? round(($range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->count()/$range_old->where('group_name', $group)->where('module', $value)->count())*100, 2) : 0;

                        $content = [
                            'group' => $group,
                            'module' => $value,
                            'renew_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->count(),
                            'new_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->count(),
                            'total_old' => $range_old->where('group_name', $group)->where('module', $value)->count(),
                            'renew' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->count(),
                            'new' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->count(),
                            'total' => $range_actual->where('group_name', $group)->where('module', $value)->count(),
                            'retention' => $retention.'%'
                        ];

                        array_push($table_groups_modules, $content);
                    }

                }

                $retention_g_t = $range_old->unique('license_id')->where('group_name', '!=', NULL)->where('module', $value)->count() > 0 ? round(($range_actual->unique('license_id')->where('group_name', '!=', NULL)->where('module', $value)->where('renewed_group_module', true)->count()/$range_old->unique('license_id')->where('group_name', '!=', NULL)->where('module', $value)->count())*100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'module' => 'Todos',
                    'renew_old' => $range_old->where('group_name', '!=', NULL)->unique('license_id')->where('renewed_group_module', true)->where('renewed',true)->count(),
                    'new_old' => $range_old->where('group_name', '!=', NULL)->unique('license_id')->where('renewed_group_module',false)->where('renewed',false)->count(),
                    'total_old' => $range_old->where('group_name', '!=', NULL)->unique('license_id')->count(),
                    'renew' => $range_actual->where('group_name', '!=', NULL)->where('renewed_group_module', true)->unique('license_id')->where('renewed',true)->count(),
                    'new' => $range_actual->where('group_name', '!=', NULL)->where('renewed',false)->where('renewed_group_module',false)->unique('license_id')->count(),
                    'total' => $range_actual->where('group_name', '!=', NULL)->unique('license_id')->count(),
                    'retention' => $retention_g_t.'%'
                ];

                array_push($table_groups_modules, $content2);

                $retention_general = round(($range_actual->where('renewed', true)->unique('license_id')->count()/$range_old->unique('license_id')->count())*100, 2);

                $table_general = [
                    [
                        'renew_old' => $range_old->where('renewed', true)->unique('license_id')->count(),
                        'new_old' => $range_old->where('renewed',false)->unique('license_id')->count(),
                        'total_old' => $range_old->unique('license_id')->count(),
                        'renew' => $range_actual->where('renewed', true)->unique('license_id')->count(),
                        'new' => $range_actual->where('renewed',false)->unique('license_id')->count(),
                        'total' => $range_actual->unique('license_id')->count(),
                        'retention' => $retention_general
                    ]
                ];
            }
            /*else
            {
                $headers['general'] = [                    
                    ['name' => 'new', 'label' => 'Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Licencias Renovadas']
                ];

                $headers['module'] = [     
                    ['name' => 'module', 'label' => 'Módulo'],
                    ['name' => 'new', 'label' => 'Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Licencias Renovadas']
                ];

                $headers['group'] = [                       
                    ['name' => 'group', 'label' => 'Grupo de compañia'],
                    ['name' => 'new', 'label' => 'Periodo Actual Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Periodo Actual Licencias Renovadas']
                ];

                $modules_all = $prueba->pluck('module')->unique()->values();
                $groups = $prueba->filter(function ($item, $key) {
                    return $item->group_name;
                })
                ->pluck('group_name')->unique()->values();

                $table_general = [
                    [
                        'renew' => $prueba->where('renewed', true)->unique('license_id')->count(),
                        'new' => $prueba->where('renewed',false)->unique('license_id')->count()
                    ]
                ];

                foreach ($modules_all as $key => $value) 
                {
                    $content = [
                        'module' => $value,
                        'renew' => $prueba->where('module', $value)->where('renewed_module', true)->count(),
                        'new' => $prueba->where('module', $value)->where('renewed_module',false)->count(),
                    ];
                    
                    array_push($table_module, $content);
                }

                foreach ($groups as $key => $group) {
                    $content = [
                        'group' => $group,
                        'renew' => $prueba->where('renewed', true)->where('group_name', $group)->unique('license_id')->count(),
                        'new' => $prueba->where('renewed', false)->where('group_name', $group)->unique('license_id')->count()
                    ];

                    array_push($table_groups, $content);
                }

                $content = [
                    'group' => 'Sin grupo',
                    'renew' => $prueba->where('renewed', true)->where('group_name', NULL)->unique('license_id')->count(),
                    'new' => $prueba->where('renewed', false)->where('group_name', NULL)->unique('license_id')->count()
                ];

                array_push($table_groups, $content);

            }*/


////////////////////////////////////////////////////////////////////////////

            $data = [
                'headers' => $headers,
                'data' => [
                    'general' => $table_general,
                    'module' => $table_module,
                    'group' => $table_groups,
                    'group_module' => $table_groups_modules
                ],
            ];

            //\Log::info($data);

            return $data;


        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
