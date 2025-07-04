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
use App\Jobs\System\Licenses\ExportLicensesReportJob;
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
                    sau_companies.name AS company,
                    sau_company_groups.name AS group_company'
            )
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->where('sau_modules.main', 'SI')
            ->groupBy('sau_licenses.id')
            ->orderBy('sau_licenses.id', 'DESC');

        $url = "/system/licenses";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["modules"]) && $filters["modules"])
                $licenses->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);

            if (isset($filters["groups"]) && $filters["groups"])
                $licenses->inGroups($this->getValuesForMultiselect($filters["groups"]), $filters['filtersType']['groups']);

            if (isset($filters["freeze"]) && $filters["freeze"])
                $licenses->inFreeze($this->getValuesForMultiselect($filters["freeze"]), $filters['filtersType']['freeze']);
                
                
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $licenses->betweenDate($dates);
        }

        $now = Carbon::now()->format('Y-m-d');

        return Vuetable::of($licenses)
                /*->addColumn('system-licenses-edit', function ($license) use ($now) {
                    $end_at = Carbon::parse($license->ended_at);
                    $started_at = Carbon::parse($now);

                    if ($end_at > $started_at)
                        return true;
                    else
                        return false;
                })*/
                ->make();
    }

    public function dataReasignacion(Request $request)
    {
        $licenses = License::system()
            ->selectRaw(
                'sau_licenses.*,
                    GROUP_CONCAT(" ", sau_modules.display_name ORDER BY sau_modules.display_name) AS modules,
                    sau_companies.name AS company,
                    sau_company_groups.name AS group_company'
            )
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->join('sau_license_module_freeze', 'sau_license_module_freeze.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module_freeze.module_id')
            ->where('sau_modules.main', 'SI')
            ->where('sau_licenses.freeze', 'SI')
            ->whereNull('sau_licenses.reassigned')
            ->groupBy('sau_licenses.id')
            ->orderBy('sau_licenses.id', 'DESC');

        $url = "/system/licenses";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["modules"]) && $filters["modules"])
                $licenses->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);

            if (isset($filters["groups"]) && $filters["groups"])
                $licenses->inGroups($this->getValuesForMultiselect($filters["groups"]), $filters['filtersType']['groups']);                
                
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
            $license->freeze = 'NO';
            
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
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la licencia'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\System\Licenses\LicenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function saveReasignar(LicenseRequest $request)
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
            $license = new License();
            $license->started_at = (Carbon::createFromFormat('D M d Y', $request->started_at))->format('Y-m-d');
            $license->ended_at = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Ymd');
            $license->company_id = $request->company_id;
            $license->notified = 'NO';

            //$license = new License($request->all());
            
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

            $license_origin = License::system()->findOrFail($request->id_license);
            $license_origin->reassigned = 'SI';
            $license_origin->save();

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Creación');

        } catch(\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se reasigno la licencia'
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
            $license->multiselect_user = $license->user_id ? $license->user->multiselect() : NULL;
            $license->started_at = (Carbon::createFromFormat('Y-m-d', $license->started_at))->format('D M d Y');
            $license->ended_at = (Carbon::createFromFormat('Y-m-d', $license->ended_at))->format('D M d Y');
            $license->start_freeze = $license->start_freeze ? (Carbon::createFromFormat('Y-m-d', $license->start_freeze))->format('D M d Y') : NULL;

            $modules = [];
            $modules_freeze = [];

            $mails = [];

            foreach ($license->modules()->main()->get() as $key => $value)
            {               
                array_push($modules, $value->multiselect());
            }

            $license->module_id = $modules;

            $license->add_email = $mails;

            $now = Carbon::now()->format('Y-m-d');

            $end_at = Carbon::parse($license->ended_at);
            $started_at = Carbon::parse($now);

            $license->vigency = $end_at > $started_at;

            if ($license->freeze == 'SI')
            {
                foreach ($license->modulesFreeze()->main()->get() as $key => $value)
                {               
                    array_push($modules_freeze, $value->multiselect());
                }
            }

            $license->module_freeze = $modules_freeze;

            return $this->respondHttp200([
                'data' => $license,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function showReasignar($id)
    {
        try
        {
            $license = License::system()->findOrFail($id);
            $license->multiselect_company = $license->company->multiselect();
            $license->multiselect_user = NULL;
            $license->user_id = NULL;
            //$license->started_at = Carbon::now()->format('D M d Y');
            $inicio = (Carbon::createFromFormat('Y-m-d', $license->start_freeze))->addDays(1);
            $license->started_at = $inicio->format('D M d Y');
            $end = $inicio->addDays($license->available_days)->format('D M d Y');
            $license->ended_at = $end;

            $modules = [];

            $mails = [];

            foreach ($license->modulesFreeze()->main()->get() as $key => $value)
            {               
                array_push($modules, $value->multiselect());
            }

            $license->modules_quantity = count($modules);

            $license->module_id = $modules;

            $license->add_email = $mails;
            $license->id_license = $license->id;
            $license->id = NULL;
            $license->freeze = 'NO';
            $license->group_company = $license->company->company_group_id;

            return $this->respondHttp200([
                'data' => $license,
            ]);

        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function updateEndedAt(Request $request)
    {
        $end = Carbon::parse($request->ini)->addDays($request->days)->format('D M d Y');

        return $end;
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
            $license->start_freeze = $request->start_freeze ? (Carbon::createFromFormat('D M d Y', $request->start_freeze))->format('Y-m-d') : NULL;

            if ($license->started_at != $old_started)
                array_push($modificaciones, ['fecha_inicio' => $license->started_at]);

            if ($license->ended_at != $old_ended)
                array_push($modificaciones, ['fecha_fin' => $license->ended_at]);

            $modulos_old = $license->modules()->where('main', 'SI')->get();

            $modulos_delete = [];
            $modulos_freeze = [];

            if ($old_company != $license->company_id || $old_ended != $license->ended_at)
                $license->notified = 'NO';
            
            if (!$license->update())
                return $this->respondHttp500();

            $modules_main = $this->getDataFromMultiselect($request->get('module_id'));
            $modules = ModuleDependence::whereIn('module_id', $modules_main)->pluck('module_dependence_id')->toArray();

            $modules_freeze = $request->has('module_freeze') && count($request->get('module_freeze')) > 0 ? $this->getDataFromMultiselect($request->get('module_freeze')) : [];
            $modules_f = ModuleDependence::whereIn('module_id', $modules_freeze)->pluck('module_dependence_id')->toArray();

            foreach ($modulos_old as $key => $value) 
            {
                if (!in_array($value->id, $modules_main))
                {
                    array_push($modulos_delete, $value->display_name);
                }
            }

            if (count($modules_freeze) > 0)
            {
                foreach ($modulos_old as $key => $value) 
                {
                    if (in_array($value->id, $modules_freeze))
                    {
                        array_push($modulos_freeze, $value->id);
                    }
                }
            }

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);

            if ($license->freeze == 'SI')
            {
                $license->modulesFreeze()->sync(array_merge($modules_freeze, $modules_f));
                $license->modules()->sync(array_merge($modules_freeze, $modules_f));

                $end = (Carbon::createFromFormat('D M d Y', $request->ended_at))->format('Y-m-d');

                $date1 = Carbon::parse($license->start_freeze);
                $date2 = Carbon::parse($end);

                $days_available = $date1->diffInDays($date2);

                $license->available_days = $days_available;
                $license->observations = $request->observations;
                $license->date_freeze = Carbon::now()->format('Y-m-d');
                $license->save();

                if (count($modules_main) > count($modules_freeze))
                {
                    $mudules_license_new = [];

                    foreach ($modules_main as $key => $modul) 
                    {
                        if (!in_array($modul, $modules_freeze))
                        {
                            array_push($mudules_license_new, $modul);
                        }
                    }

                    if (count($mudules_license_new) > 0)
                    {
                        $this->saveLicenseNewFreeze($license, $mudules_license_new);
                    }
                }
            }
            else
            {
                $license->modules()->sync(array_merge($modules_main, $modules));
                $license->available_days = NULL;
                $license->save();
            }
            
            DB::commit();
            
            $mails = $request->has('add_email') ? $this->getDataFromMultiselect($request->get('add_email')) : [];

            NotifyLicenseRenewalJob::dispatch($license->id, $license->company_id, $modules_main, $mails, 'Modificación', $modificaciones, $modulos_delete, $license->freeze, $license->observations, $modulos_freeze);

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la licencia'
        ]);
    }

    public function saveLicenseNewFreeze($request, $modules_new)
    {
        DB::beginTransaction();

        try
        {
            $license = new License();
            $license->started_at = $request->started_at;
            $license->ended_at = $request->ended_at;
            $license->company_id = $request->company_id;
            $license->notified = 'NO';
            
            if (!$license->save())
                return $this->respondHttp500();

            $modules = ModuleDependence::whereIn('module_id', $modules_new)->pluck('module_dependence_id')->toArray();
            $license->modules()->sync(array_merge($modules_new, $modules));

            $license->histories()->create([
                'user_id' => $this->user->id
            ]);

            DB::commit();


        } catch(\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se reasigno la licencia'
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
            $order = $request->order;

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
                sau_company_groups.name as group_name,
                sau_companies.name as name_company
            ")
            ->withoutGlobalScopes()
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            ->where('sau_companies.test', DB::raw("'NO'"))
            ->orderBy('sau_licenses.id');

            if (isset($filters["freeze"]) && $filters["freeze"])
                $prueba->inFreeze($this->getValuesForMultiselect($filters["freeze"]), $filters['filtersType']['freeze']);

            $prueba = $prueba->get();

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
                ['name' => 'retention', 'label' => 'Porcentaje de retención'],
                ['name' => 'crecimiento', 'label' => 'Porcentaje de crecimiento']
            ];

            $headers['group'] = [                       
                ['name' => 'group', 'label' => 'Grupo de compañia'],                 
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención'],
                ['name' => 'crecimiento', 'label' => 'Porcentaje de crecimiento']
            ];

            $headers['module'] = [      
                ['name' => 'module', 'label' => 'Módulo'],
                ['name' => 'new_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Nuevas'],
                ['name' => 'renew_old', 'label' => 'Periodo '.$dates_old[0].'/'.$dates_old[1].' Licencias Renovadas'],
                ['name' => 'total_old', 'label' => 'Total Periodo '.$dates_old[0].'/'.$dates_old[1]],
                ['name' => 'new', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Nuevas'],
                ['name' => 'renew', 'label' => 'Periodo '.$dates[0].'/'.$dates[1].' Licencias Renovadas'],
                ['name' => 'total', 'label' => 'Total Periodo '.$dates[0].'/'.$dates[1]],
                ['name' => 'retention', 'label' => 'Porcentaje de retención'],
                ['name' => 'crecimiento', 'label' => 'Porcentaje de crecimiento']
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
                ['name' => 'retention', 'label' => 'Porcentaje de retención'],
                ['name' => 'crecimiento', 'label' => 'Porcentaje de crecimiento']
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

                    $crecimiento = $range_old->where('group_name', $group)->count() > 0 ? round((($range_actual->where('group_name', $group)->count() - $range_old->where('group_name', $group)->count())/$range_old->where('group_name', $group)->count()) * 100, 2) : 0;

                    $content = [
                        'group' => $group,
                        'renew_old' => $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total_old' => $range_old->where('group_name', $group)->count(),
                        'renew' => $range_actual->where('group_name', $group)->where('renewed_module', true)->where('renewed', true)->count(),
                        'new' => $range_actual->where('group_name', $group)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('renewed', true)->where('renewed_module', false)->count(),
                        'total' => $range_actual->where('group_name', $group)->count(),
                        'retention' => $retention,
                        'crecimiento' => $crecimiento
                    ];

                    array_push($table_groups, $content);
                }


                $retention_sg = $range_old->where('group_name', NULL)->count() > 0 ? round(($range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count()/$range_old->where('group_name', NULL)->count())*100, 2) : 0;

                $crecimiento_sg = $range_old->where('group_name', NULL)->count() > 0 ? round((($range_actual->where('group_name', NULL)->count() - $range_old->where('group_name', NULL)->count())/$range_old->where('group_name', NULL)->count()) * 100, 2) : 0;

                $content = [
                    'group' => 'Sin grupo',
                    'renew_old' => $range_old->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new_old' => $range_old->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total_old' => $range_old->where('group_name', NULL)->count(),
                    'renew' => $range_actual->where('group_name', NULL)->where('renewed', true)->where('renewed_module', true)->count(),
                    'new' => $range_actual->where('group_name', NULL)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', NULL)->where('renewed',true)->where('renewed_module', false)->count(),
                    'total' => $range_actual->where('group_name', NULL)->count(),
                    'retention' => $retention_sg,
                    'crecimiento' => $crecimiento_sg
                ];

                $retention_sg_t = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_sg_t = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed',false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed',false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_sg_t,
                    'crecimiento' => $crecimiento_sg_t
                ];

                array_push($table_groups, $content);

                $table_groups = collect($table_groups)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0 || $item['crecimiento'] > 0;
                })->sortBy($order)->values();

                $table_groups->push($content2);

                foreach ($modules_all as $key => $value) 
                {
                    $retention = $range_old->where('module', $value)->count() > 0 ? round(($range_actual->where('module', $value)->where('renewed_module', true)->count()/$range_old->where('module', $value)->count())*100, 2) : 0;

                    $crecimiento = $range_old->where('module', $value)->count() > 0 ? round((($range_actual->where('module', $value)->count() - $range_old->where('module', $value)->count())/$range_old->where('module', $value)->count()) * 100, 2) : 0;

                    $content = [
                        'module' => $value,
                        'renew_old' => $range_old->where('module', $value)->where('renewed_module', true)->count(),
                        'new_old' => $range_old->where('module', $value)->where('renewed_module',false)->count(),
                        'total_old' => $range_old->where('module', $value)->count(),
                        'renew' => $range_actual->where('module', $value)->where('renewed_module', true)->count(),
                        'new' => $range_actual->where('module', $value)->where('renewed_module',false)->count(),
                        'total' => $range_actual->where('module', $value)->count(),
                        'retention' => $retention,
                        'crecimiento' => $crecimiento
                    ];

                    array_push($table_module, $content);
                }

                $table_module = collect($table_module)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0;
                })->sortBy($order)->values();

                $retention_m = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_m = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'module' => 'Total',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed', false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed', false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_m,
                    'crecimiento' => $crecimiento_m
                ];

                $table_module->push($content2);

                foreach ($groups as $key => $group) 
                {
                    foreach (collect($grupos_modulos[$group])->unique()->values() as $key => $value) 
                    {
                        $retention = $range_old->where('group_name', $group)->where('module', $value)->count() > 0 ? round(($range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count()/$range_old->where('group_name', $group)->where('module', $value)->count())*100, 2) : 0;

                        $crecimiento = $range_old->where('group_name', $group)->where('module', $value)->count() > 0 ? round((($range_actual->where('group_name', $group)->where('module', $value)->count() - $range_old->where('group_name', $group)->where('module', $value)->count())/$range_old->where('group_name', $group)->where('module', $value)->count()) * 100, 2) : 0;

                        $content = [
                            'group' => $group,
                            'module' => $value,
                            'renew_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count(),
                            'new_old' => $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',true)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',false)->where('renewed_module', false)->count() + $range_old->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', false)->count(),
                            'total_old' => $range_old->where('group_name', $group)->where('module', $value)->count(),
                            'renew' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed',true)->where('renewed_module', true)->count(),
                            'new' => $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',false)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module',false)->where('renewed',true)->where('renewed_module', false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed_module', false)->where('renewed',false)->count() + $range_actual->where('group_name', $group)->where('module', $value)->where('renewed_group_module', true)->where('renewed_module', false)->where('renewed',true)->count(),
                            'total' => $range_actual->where('group_name', $group)->where('module', $value)->count(),
                            'retention' => $retention,
                            'crecimiento' => $crecimiento
                        ];

                        array_push($table_groups_modules, $content);
                    }

                }

                $table_groups_modules = collect($table_groups_modules)->filter(function ($item, $key) {
                    return $item['renew_old'] > 0 ||  $item['new_old'] > 0 ||  $item['total_old'] > 0 ||  $item['renew'] > 0 ||  $item['new'] > 0 ||  $item['total'] > 0  || $item['retention'] > 0;
                })->sortBy($order)->values();

                $retention_g_t = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_g_t = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100, 2) : 0;

                $content2 = [
                    'group' => 'Total',
                    'module' => 'Todos',
                    'renew_old' => $range_old->where('renewed', true)->count(),
                    'new_old' => $range_old->where('renewed',false)->count(),
                    'total_old' => $range_old->count(),
                    'renew' => $range_actual->where('renewed', true)->count(),
                    'new' => $range_actual->where('renewed',false)->count(),
                    'total' => $range_actual->count(),
                    'retention' => $retention_g_t,
                    'crecimiento' => $crecimiento_g_t
                ];


                $table_groups_modules->push($content2);

                $retention_general = $range_old->count() > 0 ? round(($range_actual->where('renewed', true)->count()/$range_old->count())*100, 2) : 0;

                $crecimiento_general = $range_old->count() > 0 ? round((($range_actual->count() - $range_old->count())/$range_old->count()) * 100) : 0;

                $table_general = [
                    [
                        'renew_old' => $range_old->where('renewed', true)->count(),
                        'new_old' => $range_old->where('renewed',false)->count(),
                        'total_old' => $range_old->count(),
                        'renew' => $range_actual->where('renewed', true)->count(),
                        'new' => $range_actual->where('renewed',false)->count(),
                        'total' => $range_actual->count(),
                        'retention' => $retention_general,
                        'crecimiento' => $crecimiento_general
                    ]
                ];
            }

///////////////Reporte grupo - compañia - modulos que no posee//////////////


            $table_not_module = [];

            $prueba2 = License::selectRaw("
                sau_companies.id as company_id,
                sau_licenses.id as license_id,
                sau_modules.display_name as module,
                sau_licenses.started_at as fecha,
                sau_company_groups.name as group_name,
                sau_companies.name as name_company
            ")
            ->withoutGlobalScopes()
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
            ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
            ->leftJoin('sau_company_groups', 'sau_company_groups.id', 'sau_companies.company_group_id')
            ->where('sau_modules.main', DB::raw("'SI'"))
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_companies.test', DB::raw("'NO'"));

            $modules_totales = Module::select('display_name')->where('main', DB::raw("'SI'"));

            if (isset($filters["modules"]) && $filters["modules"])
            {
                $modules_filters = $this->getValuesForMultiselect($filters["modules"]);
                $modules_totales->whereIn('sau_modules.id', $modules_filters);
            }
        
            $prueba2 = $prueba2->orderBy('sau_licenses.id')->get();

            $modules_totales = $modules_totales->pluck('display_name')->toArray();

            foreach ($groups as $key => $group) 
            {
                $companies = $prueba2->filter(function ($item, $key) use ($group) {
                    return $item->group_name == $group;
                })->pluck('name_company')->unique()->values();

                foreach ($companies as $key => $company) 
                {
                    $modules_disponibles = [];
                    $modules_company = $prueba2->filter(function ($item, $key) use ($group, $company){
                        return $item->group_name == $group && $item->name_company == $company;
                    })
                    ->pluck('module')->unique()->values();
                    
                    foreach ($modules_company as $key => $company_module) 
                    {
                        if (is_array($company_module) && COUNT($company_module) > 0)
                            array_push($modules_disponibles, $company_module);
                        else if (is_string($company_module))
                            array_push($modules_disponibles, $company_module);
                    }

                    $content = [];

                    foreach ($modules_totales as $key => $module) 
                    {
                    if (!in_array($module, $modules_disponibles))
                        array_push($content, $module);
                    }

                    if (COUNT($content) > 0)
                    {
                        $content = [
                            'group' => $group,
                            'company' => $company,
                            'module' => implode(' - ',$content)
                        ];

                        array_push($table_not_module, $content);
                    }
                }
            }

            $companies_sin_grupo = $prueba2->filter(function ($item, $key) {
                return !$item->group_name;
            })
            ->pluck('name_company')->unique()->values();

            foreach ($companies_sin_grupo as $key => $company) 
            {
                $modules_disponibles = [];
                $modules_company = $prueba2->filter(function ($item, $key) use ($company){
                    return !$item->group_name && $item->name_company == $company;
                })
                ->pluck('module')->unique()->values();
                
                foreach ($modules_company as $key => $company_module) 
                {
                    if (is_array($company_module) && COUNT($company_module) > 0)
                        array_push($modules_disponibles, $company_module);
                    else if (is_string($company_module))
                        array_push($modules_disponibles, $company_module);
                }

                $content = [];

                foreach ($modules_totales as $key => $module) 
                {
                if (!in_array($module, $modules_disponibles))
                    array_push($content, $module);
                }

                if (COUNT($content) > 0)
                {
                    $content = [
                        'group' => 'Sin grupo',
                        'company' => $company,
                        'module' => implode(' - ',$content)
                    ];

                    array_push($table_not_module, $content);
                }
            }

            $headers['group_module_not'] = [      
                ['name' => 'group', 'label' => 'Grupo de compañia'],
                ['name' => 'company', 'label' => 'compañia'],
                ['name' => 'module', 'label' => 'Módulo'],
            ];


///////////////////////////////////////////////////////////////////////////

            $data = [
                'headers' => $headers,
                'data' => [
                    'general' => $table_general,
                    'module' => $table_module,
                    'group' => $table_groups,
                    'group_module' => $table_groups_modules,
                    'group_module_not' => $table_not_module
                ],

            ];

            //\Log::info($data);

            return $data;


        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function exportReport(Request $request)
    {
        $data = $this->report($request);

        ExportLicensesReportJob::dispatch($this->user, $this->company, $data);
    }
}
