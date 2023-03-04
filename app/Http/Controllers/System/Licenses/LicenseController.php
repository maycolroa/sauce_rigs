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
            $headers = [
                'general' => [],
                'module' => [],
                'grupo' => []
            ];
            $data = [
                'general' => [],
                'module' => [],
                'grupo' => []
            ];
            $dates = [];
            $dates_old = [];
            $dates_request = explode('/', $request->dateRange);
    
            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
                array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));

                array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->subYear(1)->format('Y-m-d'));
                array_push($dates_old, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->subYear(1)->format('Y-m-d'));

            }

///////////////////////////Reporte general comienza///////////////////////////

                /*$general = License::selectRaw('
                    company_id,
                    count(id) as cant
                ')
                ->groupBy('company_id')
                ->withoutGlobalScopes();

            if (COUNT($dates) > 0)
            {            
                $headers['general'] = [                    
                    'Periodo Anterior Licencias Nuevas',
                    'Periodo Anterior Licencias Renovadas',
                    'Periodo Actual Licencias Nuevas',
                    'Periodo Actual Licencias Renovadas'
                ];

                $general->whereBetween('sau_licenses.started_at', $dates);

                $general_complete = $general->havingRaw("count(l.id) > 1")->get();

                $report_general = DB::table(DB::raw("({$general->toSql()}) AS t"))
                ->selectRaw("
                    sum(1) AS nuevas_actual,
                    sum(case when t.cant > 1 then t.cant - 1 end) as renovadas_actual
                ")
                ->mergeBindings($general->getQuery())
                ->get()->toArray();



                $general2 = License::selectRaw('
                    0 as cant_actual,
                    count(id) as cant_old'
                )
                ->groupBy('company_id')
                ->withoutGlobalScopes()
                ->whereBetween('sau_licenses.started_at', $dates_old);
            
                
                $report_general = DB::table(DB::raw("({$general->toSql()}) AS t"))
                ->selectRaw("
                    sum(1) AS nuevas,
                    sum(case when t.cant > 1 then t.cant - 1 end) as renovadas
                ")
                ->mergeBindings($general->getQuery())
                ->get()->toArray();
            }
            else
            {
                $headers['general'] = [                    
                    'Licencias Nuevas',
                    'Licencias Renovadas'
                ];
                $report_general = DB::table(DB::raw("({$general->toSql()}) AS t"))
                ->selectRaw("
                    sum(1) AS nuevas,
                    sum(case when t.cant > 1 then t.cant - 1 end) as renovadas
                ")
                ->mergeBindings($general->getQuery())
                ->get()->toArray();
            }

///////////////////////////Reporte general termina////////////////////////////
///////////////////////////Reporte Modulos comienza///////////////////////////

            $modules = Module::selectRaw("
                sau_modules.display_name as display_name,
                count(sau_licenses.id) as cant
            ")
            ->join('sau_license_module', 'sau_license_module.module_id', 'sau_modules.id')
            ->join('sau_licenses', 'sau_licenses.id', 'sau_license_module.license_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            ->groupBy('sau_modules.id', 'sau_licenses.company_id');

            if (COUNT($dates) > 0)
            {
                $headers['module'] = [      
                    'Módulo',              
                    'Periodo Anterior Licencias Nuevas',
                    'Periodo Anterior Licencias Renovadas',
                    'Periodo Actual Licencias Nuevas',
                    'Periodo Actual Licencias Renovadas'
                ];

                $modules->whereBetween('sau_licenses.started_at', $dates);
            }
            else
            {
                $headers['module'] = [     
                    'Modulo',
                    'Licencias Nuevas',
                    'Licencias Renovadas'
                ];
            }

            $report_modules = DB::table(DB::raw("({$modules->toSql()}) AS t"))
            ->selectRaw("
                t.display_name as module,
                sum(1) AS nuevas,
                sum(case when t.cant > 1 then t.cant - 1 end) as renovadas
            ")
            ->mergeBindings($modules->getQuery())
            ->groupBy('t.display_name')
            ->get()->toArray();

///////////////////////////Reporte Modulos termina///////////////////////////
///////////////////////////Reporte Grupos comienza///////////////////////////

            $group = License::selectRaw('
                count(sau_licenses.id) as cant,
                sau_company_groups.name as grupo
            ')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->join('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->groupBy('company_group_id')
            ->withoutGlobalScopes();

            if (COUNT($dates) > 0)
            {
                $headers['group'] = [                
                    'Grupo de compañia',
                    'Periodo Anterior Licencias Nuevas',
                    'Periodo Anterior Licencias Renovadas',
                    'Periodo Actual Licencias Nuevas',
                    'Periodo Actual Licencias Renovadas'
                ];

                $group->whereBetween('sau_licenses.started_at', $dates);
            }
            else
            {
                $headers['group'] = [                
                    'Grupo de compañia',
                    'Licencias Nuevas',
                    'Licencias Renovadas'
                ];
            }

            $report_group = DB::table(DB::raw("({$group->toSql()}) AS t"))
            ->selectRaw("
                t.grupo,
                sum(1) AS nuevas,
                sum(case when t.cant > 1 then t.cant - 1 end) as renovadas
            ")
            ->mergeBindings($group->getQuery())
            ->groupBy('t.grupo')
            ->get()->toArray();*/

///////////////////////////Reporte Grupos termina///////////////////////////


///////////////////////Prueba//////////////////////////////////////////////

            $id_license_renew = [];
            $id_module_renew = [];
            $id_group_renew = [];
            $table_general = [];
            $table_module = [];
            $table_groups = [];

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

            /*$companies_group = $prueba->filter(function ($item, $key) {
                return $item->group_name;
            })
            ->groupBy('group_name')
            ->each(function($item, $key) use (&$id_group_renew) {
                $i = 0;
                foreach ($item->groupBy('license_id')->all() as $license_id => $license)
                {
                    if ($i > 0)
                        array_push($id_group_renew, $license_id);

                    $i++;
                }
            });*/

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

            $prueba = $prueba->map(function ($item, $key) use ($id_license_renew, $id_module_renew) {
                $item->renewed = in_array($item->license_id, $id_license_renew);
                $item->renewed_module = isset($id_module_renew[$item->module]) && in_array($item->license_id, $id_module_renew[$item->module]);
                //$item->renewed_group = in_array($item->license_id, $id_group_renew);

                return $item;
            });

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

                $headers['general'] = [                    
                    ['name' => 'new_old', 'label' => 'Periodo Anterior Licencias Nuevas'],
                    ['name' => 'renew_old', 'label' => 'Periodo Anterior Licencias Renovadas'],
                    ['name' => 'new', 'label' => 'Periodo Actual Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Periodo Actual Licencias Renovadas']
                ];

                $headers['group'] = [                       
                    ['name' => 'group', 'label' => 'Grupo de compañia'],                 
                    ['name' => 'new_old', 'label' => 'Periodo Anterior Licencias Nuevas'],
                    ['name' => 'renew_old', 'label' => 'Periodo Anterior Licencias Renovadas'],
                    ['name' => 'new', 'label' => 'Periodo Actual Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Periodo Actual Licencias Renovadas']
                ];

                $headers['module'] = [      
                    ['name' => 'module', 'label' => 'Módulo'],
                    ['name' => 'new_old', 'label' => 'Periodo Anterior Licencias Nuevas'],
                    ['name' => 'renew_old', 'label' => 'Periodo Anterior Licencias Renovadas'],
                    ['name' => 'new', 'label' => 'Periodo Actual Licencias Nuevas'],
                    ['name' => 'renew', 'label' => 'Periodo Actual Licencias Renovadas']
                ];

                $groups = $prueba->filter(function ($item, $key) {
                    return $item->group_name;
                })
                ->pluck('group_name')->unique()->values();

                foreach ($groups as $key => $group) {
                    $content = [
                        'group' => $group,
                        'renew_old' => $range_old->where('group_name', $group)->where('renewed', true)->count(),
                        'new_old' => $range_old->where('group_name', $group)->where('renewed',false)->count(),
                        'renew' => $range_actual->where('group_name', $group)->where('renewed', true)->count(),
                        'new' => $range_actual->where('group_name', $group)->where('renewed',false)->count()
                    ];

                    array_push($table_groups, $content);
                }

                foreach ($modules_all as $key => $value) 
                {
                    $content = [
                        'module' => $value,
                        'renew_old' => $range_old->where('module', $value)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('module', $value)->where('renewed_module',false)->count(),
                        'renew' => $range_actual->where('module', $value)->where('renewed_module', true)->count(),
                        'new' => $range_actual->where('module', $value)->where('renewed_module',false)->count()
                    ];

                    array_push($table_module, $content);
                }

                $table_general = [
                    [
                        'renew_old' => $range_old->where('renewed', true)->count(),
                        'new_old' => $range_old->where('renewed',false)->count(),
                        'renew' => $range_actual->where('renewed', true)->count(),
                        'new' => $range_actual->where('renewed',false)->count(),
                    ]
                ];
            }
            else
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

            }


////////////////////////////////////////////////////////////////////////////

            $data = [
                'headers' => $headers,
                'data' => [
                    'general' => $table_general,
                    'module' => $table_module,
                    'group' => $table_groups
                ],
            ];

            //\Log::info($data);

            return $data;


        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
