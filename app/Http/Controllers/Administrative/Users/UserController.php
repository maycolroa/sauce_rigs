<?php

namespace App\Http\Controllers\Administrative\Users;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Users\UserRequest;
use App\Http\Requests\Administrative\Users\ChangePasswordRequest;
use App\Http\Requests\Administrative\Users\DefaultModuleRequest;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\LogUserModify;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Module;
use App\Models\General\Team;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\General\LogDelete;
use App\Models\General\LogUserActivitySystem;
use App\Traits\UserTrait;
use App\Traits\ContractTrait;
use App\Traits\PermissionTrait;
use App\Jobs\Administrative\Users\UserExportJob;
use App\Jobs\System\Companies\SyncUsersSuperadminJob;
use App\Http\Requests\Administrative\Users\UserOtherCompanyRequest;
use App\Exports\Administrative\Users\UsersImportTemplate;
use App\Jobs\Administrative\Users\UserImportJob;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Filtertrait;
use Illuminate\Support\Facades\Storage;
use Validator;
use Hash;
use DB;

class UserController extends Controller
{
    use UserTrait;
    use ContractTrait;
    use PermissionTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:users_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:users_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:users_u, {$this->team}", ['only' => 'update']);
        //$this->middleware("permission:users_d, {$this->team}", ['only' => 'destroy']);
    }
    
    /**
     * Display index.
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
        $url = "/administrative/users";

        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $users = User::select(
                    'sau_users.id AS id',
                    'sau_users.name AS name',
                    'sau_users.email AS email',
                    'sau_users.document AS document',
                    'sau_users.document_type AS document_type',
                    'sau_users.active AS active',
                    'sau_users.last_login_at AS last_login_at'
                )
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {
            $team = $this->team;
            $role = Role::defined()->where('name', 'Superadmin')->first();
            
            $users = User::select(
                'sau_users.id AS id',
                'sau_users.name AS name',
                'sau_users.email AS email',
                'sau_users.document AS document',
                'sau_users.document_type AS document_type',
                'sau_users.active AS active',
                'sau_users.last_login_at AS last_login_at',
                DB::raw('GROUP_CONCAT(sau_roles.name) AS role')
            )
            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_role_user', function($q) use ($team) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                  ->on('sau_role_user.team_id', '=', DB::raw($team));
            })
            ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->groupBy('sau_users.id');

            $isSuper = $this->user->hasRole('Superadmin', $this->team);

            if (!$isSuper)
                $users->where('sau_role_user.role_id', '<>', $role->id)
                      /*->orWhereNull('sau_role_user.role_id')*/;
        }
            
        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $users->inRoles($this->getValuesForMultiselect($filters["roles"]), $filters['filtersType']['roles']);
        }

        $users = $users->orderBy('sau_users.id', 'DESC');
        

       return Vuetable::of($users)
                ->addColumn('administrative-users-edit', function ($user) {
                    $isSuper = $this->user->hasRole('Superadmin', $this->team);

                    if ($user->id != $this->user->id)
                    {
                        if ($isSuper)
                            return true;
                        else 
                            return !$user->hasRole('Superadmin', $this->team);
                    }

                    return false; 
                })
                /*->addColumn('control_delete', function ($user) {
                    return $user->id != Auth::user()->id; 
                })*/
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try
        { 
            $user = $this->createUser($request);
    
            if ($user == $this->respondHttp500() || $user == null) {
                return $this->respondHttp500();
            }
            else if ($user == "Documento repetido")
            {
                return $this->respondWithError('El documento ingresado ya se encuentra activo en el sistema, por lo tanto no puede ser procesado, por favor contacte con el administrador');
            }

            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $role = $this->user->hasRole('Arrendatario', $this->team) ? 'Arrendatario' : 'Contratista';
                $role = Role::defined()->where('name', $role)->first();

                $user->attachRole($role, $this->team);
                $contract = $this->getContractUser($this->user->id);
                $contract->users()->attach($user);
            }
            else
            {
                $roles = $this->getDataFromMultiselect($request->role_id);
                $user->attachRoles($roles, $this->team);
            }

            if ($request->has('filter_headquarter'))
            {
                $data = $this->builderArrayFilter($this->getDataFromMultiselect($request->filter_headquarter));

                if (COUNT($data) > 0)
                    $user->headquarters()->sync($data);
            }

            if ($request->has('filter_system_apply'))
            {
                $data = $this->builderArrayFilter($this->getDataFromMultiselect($request->filter_system_apply));

                if (COUNT($data) > 0)
                    $user->systemsApply()->sync($data);
            }

            $this->saveLocation($user, $request);

            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el usuario'
        ]);
    }

    private function builderArrayFilter($data)
    {
        $result = [];

        foreach ($data as $value)
        {
            $result[$value] = ['company_id' => $this->company];
        }

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
            
        try
        {
            $regionals = [];
            $headquarters = [];
            $processes = [];
            $areas = [];

            if ($user->hasRole('Arrendatario', $this->team) || $user->hasRole('Contratista', $this->team))
                $user->edit_role = false;
            else
            {
                $user->edit_role = true;
                $user->role_id = $user->multiselectRoles($this->team);
                $user->multiselect_role = $user->role_id;
            }

            $headquarters = [];

            foreach ($user->headquarters as $key => $value)
            {   
                if ($value->pivot->company_id == $this->company)
                    array_push($headquarters, $value->multiselect());
            }

            $user->multiselect_filter_headquarter = $headquarters;
            $user->filter_headquarter = $headquarters;

            $systemsApply = [];

            foreach ($user->systemsApply as $key => $value)
            {                
                if ($value->pivot->company_id == $this->company)
                    array_push($systemsApply, $value->multiselect());
            }

            if($this->user->hasRole('Superadmin', $this->team))
            {
                $user->contracts = $this->getMultiplesContracstUser($user->id, true);
            }            
            else if (!$this->user->hasRole('Arrendatario', $this->team) || !$this->user->hasRole('Contratista', $this->team))
            {
                $user->contracts = $this->getMultiplesContracstUser($user->id, false, $this->company);
            }
            else if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $user->contracts = [];
            }

            $user->multiselect_filter_system_apply = $systemsApply;
            $user->filter_system_apply = $systemsApply;

            foreach ($user->regionals as $key => $value)
            {
                array_push($regionals, $value->multiselect());
            }

            foreach ($user->headquartersFilter as $key => $value)
            {
                array_push($headquarters, $value->multiselect());
            }

            foreach ($user->processes as $key => $value)
            {
                array_push($processes, $value->multiselect());
            }

            foreach ($user->areas as $key => $value)
            {
                array_push($areas, $value->multiselect());
            }
            
            $user->multiselect_regional = $regionals;
            $user->employee_regional_id = $regionals;

            $user->multiselect_sede = $headquarters;
            $user->employee_headquarter_id = $headquarters;

            $user->multiselect_proceso = $processes;
            $user->employee_process_id = $processes;

            $user->multiselect_area = $areas;
            $user->employee_area_id = $areas;

            $data = $user->toArray();
            $data['password'] = '';
            
            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();

        try
        { 
            $log_modify = new LogUserModify;
            $log_modify->company_id = $this->company;
            $log_modify->modifier_user = $this->user->id;
            $log_modify->modified_user = $user->id;

            $modification = '';

            if ($request->name != $user->name)
                $modification = $modification . 'Se modifico el nombre - ';

            if ($request->email != $user->email)
                $modification = $modification . 'Se modifico el email - ';
        
            if ($request->document != $user->document)
                $modification = $modification . 'Se modifico el numero de documento - ';

            if ($request->active == 'NO' && $user->active == 'SI')
                $modification = $modification . 'Se desactivo el usuario - ';

            else if ($request->active == 'SI' && $user->active == 'NO')
                $modification = $modification . 'Se activo el usuario - ';

            $user->fill($request->except('password'));

            if ($request->has('password') && $request->password)
            {
                $user->password = $request->password;

                if ($user->password != $request->password)
                    $modification = $modification . 'Se modifico la contraseña - ';
            }

            if ($request->active == 'NO' && $user->companies->count() > 1)
            {
                return $this->respondWithError('Este usuario no puede ser desactivado, ya que se encuentra asociado a varias compañias');
            }
            
            if (!$user->update())
                return $this->respondHttp500();

            if (!$user->hasRole('Arrendatario', $this->team) || !$user->hasRole('Contratista', $this->team))
            {
                if ($request->get('edit_role') == 'true')
                {
                    $roles = $this->getDataFromMultiselect($request->role_id);
                    $user->syncRoles($roles, $this->team);

                    $roles_new = [];
                    $roles_old = [];

                    foreach ($request->role_id as $value) 
                    {
                        array_push($roles_new, json_decode($value)->name);
                    }

                    foreach ($request->multiselect_role as $value) 
                    {
                        array_push($roles_old, json_decode($value)->name);
                    }

                    if ($roles_old != $roles_new)
                    {
                        $modification = $modification . 'Se modifico el rol o roles del usuario - ';

                        if (COUNT($roles_old) > 0)
                            $log_modify->roles_old = implode(' | ', $roles_old);
                        
                        if (COUNT($roles_new) > 0)
                            $log_modify->roles_new = implode(' | ', $roles_new);
                    }
                }
            }

            if ($request->has('filter_headquarter'))
            {
                $data = $this->builderArrayFilter($this->getDataFromMultiselect($request->filter_headquarter));

                if (COUNT($data) > 0)
                    $user->headquarters()->sync($data);
                else
                    $this->deleteFilter('sau_reinc_user_headquarter', $user->id);
            }
            else
                $this->deleteFilter('sau_reinc_user_headquarter', $user->id);

            if ($request->has('filter_system_apply'))
            {
                $data = $this->builderArrayFilter($this->getDataFromMultiselect($request->filter_system_apply));

                if (COUNT($data) > 0)
                    $user->systemsApply()->sync($data);
                else
                    $this->deleteFilter('sau_lm_user_system_apply', $user->id);
            }
            else
                $this->deleteFilter('sau_lm_user_system_apply', $user->id);

            $log_modify->modification = $modification;
            $log_modify->save();

            $this->saveLocation($user, $request);

            DB::commit();

            SyncUsersSuperadminJob::dispatch();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el usuario'
        ]);
    }

    private function deleteFilter($table, $user_id)
    {
        DB::table($table)
            ->where('company_id', $this->company)
            ->where('user_id', $user_id)
            ->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();

        try
        {
            if ($user->companies->count() > 1)
            {
                $roles = DB::table('sau_role_user')->where('user_id', $user->id)->where('team_id', $this->company)->pluck('role_id');

                $this->saveLogDelete('Usuarios', 'Se elimino el usuario '.$user->name.'-'.$user->email.' de la compañia');

                $user->companies()->detach($this->company);
                $user->detachRoles($roles, $this->company);
            }
            else
            {
                $user->update(['active' => 'NO']);

                DB::commit();

                $this->saveLogDelete('Usuarios', 'Se desactivo el usuario '.$user->name.'-'.$user->email.' de la compañia');

                return $this->respondWithError('Este usuario no puede ser eliminado, se ha desactivado automáticamente ya que solo esta asociado a la compañia en sesión');
            }
            

            DB::commit();
            
            return $this->respondHttp200([
                'message' => 'Se elimino el usuario de la compañia'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return $this->respondHttp500();
        }
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
            $roles = $this->getValuesForMultiselect($request->roles);
            $filtersType = $request->filtersType;

            $filters = [
                'roles' => $roles,
                'filtersType' => $filtersType
            ];

            UserExportJob::dispatch($this->user, $this->company, $filters);
          
            return $this->respondHttp200();
        } 
        catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function multiselect(Request $request)
    {
        $users = User::selectRaw("
                    sau_users.id as id,
                    CONCAT(sau_users.document, ' - ', sau_users.name) as name
                ")->active();

        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                  ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {
            $users->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');
        }

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $users = $users->where(function ($query) use ($keyword) {
                        $query->orWhere('sau_users.document', 'like', $keyword)
                        ->orWhere('sau_users.name', 'like', $keyword);
                    })->get();
                    //->take(30)->pluck('id', 'name');

            $isSuper = $this->user->hasRole('Superadmin', $this->team);

            if (!$isSuper)
            {
                $users = $users->filter(function ($user, $key) {
                    return !$user->hasRole('Superadmin', $this->team);
                });
            }

            $users = $users->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($users)
            ]);
        }
        else
        {
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
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        Validator::make($request->all(), [
            "old_password" => [
                function ($attribute, $value, $fail)
                {
                    if(!Hash::check($value, $this->user->password))
                        $fail('La contraseña actual no coincide con la registrada en el sistema');
                },
            ]
        ])->validate();

        $this->user->fill($request->all());
        
        if(!$this->user->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio la contraseña'
        ]);
    }

    public function myDefaultModule()
    {
        try
        {
            return $this->respondHttp200([
                'data' => [
                    "module_id" => $this->user->module_id,
                    "multiselect_module" => $this->user->defaultModule ? $this->user->defaultModule->multiselect() : null
                ]
            ]);
        } 
        catch(Exception $e)
        {
            $this->respondHttp500();
        }
    }

    public function defaultModule(DefaultModuleRequest $request)
    {
        $url = null;
        $module_id = null;

        if ($request->has("module_id") && $request->module_id)
        {
            $module = Module::findOrFail($request->module_id);    
            $url = $module->application->name.'/'.$module->name;
            $module_id = $module->id;
        }

        $this->user->default_module_url = $url;
        $this->user->module_id = $module_id;

        if(!$this->user->update()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo su módulo favorito'
        ]);
    }

    /**
     * Undocumented function
     *
     * @return Array
     */
    public function filtersConfig()
    {
        //$includeSuper = $this->user->hasRole('Superadmin', $this->company) ? true : false;

        $roles = Role::alls(true)->get();
        $modules = Module::whereIn('name', ['legalMatrix', 'reinstatements', 'dangerousConditions'])->get();
        $mods_license = [];
        $data = [];

        foreach ($modules as $module)
        {
            $mods_license[$module->id] = $this->checkLicense($this->company, $module->id);
        }

        foreach ($roles as $role)
        {
            $mods = [];

            foreach ($modules as $module)
            {
                $result = 'NO';

                if ($mods_license[$module->id])
                {
                    if ($this->checkRolePermissionInModule($role->id, $module->id))
                    {
                        $result = 'SI';
                    }
                }

                $mods[$module->name] = $result;
            }

            $data[$role->id] = $mods;
        }

        return $data;
    }

    private function saveLocation($user, $request)
    {
        $regionals = [];
        $headquarters = [];
        $processes = [];
        $areas = [];

        $regional_alls = '';
        $headquarter_alls = '';
        $process_alls = '';
        $areas_alls = '';

        if ($request->has('employee_regional_id'))
        {
            if (count($request->employee_regional_id) > 1)
            {
                foreach ($request->employee_regional_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $regional_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_regional_id) == 1)
                $regional_alls =  json_decode($request->employee_regional_id[0])->value;
        }

        if ($request->has('employee_headquarter_id'))
        {
            if (count($request->employee_headquarter_id) > 1)
            {
                foreach ($request->employee_headquarter_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $headquarter_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_headquarter_id) == 1)
                $headquarter_alls =  json_decode($request->employee_headquarter_id[0])->value;
        }

        if ($request->has('employee_process_id'))
        {
            if (count($request->employee_process_id) > 1)
            {
                foreach ($request->employee_process_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $process_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_process_id) == 1)
                $process_alls =  json_decode($request->employee_process_id[0])->value;
        }

        if ($request->has('employee_area_id'))
        {
            if (count($request->employee_area_id) > 1)
            {
                foreach ($request->employee_area_id as $key => $value) 
                {
                    $verify = json_decode($value)->value;

                    if ($verify == 'Todos')
                    {
                        $areas_alls = 'Todos';
                        break;
                    }
                }
            }
            else if (count($request->employee_area_id) == 1)
                $areas_alls =  json_decode($request->employee_area_id[0])->value;
        }

        if ($request->has('employee_regional_id') && $regional_alls == 'Todos')
            $regionals = $this->getRegionals();

        else if ($request->has('employee_regional_id'))
            $regionals = $this->getDataFromMultiselect($request->get('employee_regional_id'));


        if ($request->has('employee_headquarter_id') && $headquarter_alls == 'Todos')
            $headquarters = $this->getHeadquarter($regionals);

        else if ($request->has('employee_headquarter_id'))
            $headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));


        if ($request->has('employee_process_id') && $process_alls == 'Todos')
            $processes = $this->getProcess($headquarters);

        else if ($request->has('employee_process_id'))
            $processes = $this->getDataFromMultiselect($request->get('employee_process_id'));


        if ($request->has('employee_area_id') && $areas_alls == 'Todos')
            $areas = $this->getAreas($headquarters, $processes);

        else if ($request->has('employee_area_id'))
            $areas = $this->getDataFromMultiselect($request->get('employee_area_id'));

        /*if ($request->has('employee_regional_id'))
            $regionals = $this->builderArrayFilter($this->getDataFromMultiselect($request->get('employee_regional_id')));



        if ($request->has('employee_headquarter_id'))
            $headquarters = $this->builderArrayFilter($this->getDataFromMultiselect($request->get('employee_headquarter_id')));



        if ($request->has('employee_process_id'))
            $processes = $this->builderArrayFilter($this->getDataFromMultiselect($request->get('employee_process_id')));



        if ($request->has('employee_area_id'))
            $areas = $this->builderArrayFilter($this->getDataFromMultiselect($request->get('employee_area_id')));*/

        $user->headquartersFilter()->sync($this->builderArrayFilter($headquarters));
        $user->regionals()->sync($this->builderArrayFilter($regionals));
        $user->processes()->sync($this->builderArrayFilter($processes));
        $user->areas()->sync($this->builderArrayFilter($areas));
    }

    private function getRegionals()
    {
        $regionals = EmployeeRegional::selectRaw(
            "sau_employees_regionals.id as id")
        ->pluck('id')
        ->toArray();

        return $regionals;
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

    public function multiselectUsers()
    {
        $users_ids = User::select('sau_users.*')
                ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->pluck('id')
                ->toArray();

        $companies_ids = User::withoutGlobalScopes()
                ->selectRaw('DISTINCT sau_company_user.company_id AS id')
                ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->where('sau_company_user.user_id', $this->user->id)
                ->where('sau_company_user.company_id', '<>', $this->company)
                ->pluck('id')
                ->toArray();

        $users = [];

        if (COUNT($users_ids) > 0 && COUNT($companies_ids) > 0)
        {
            $users = User::select(
                        "sau_users.id AS id",
                        DB::raw("CONCAT(sau_users.document, ' - ', sau_users.name) AS name")
                    )
                    ->active()
                    ->withoutGlobalScopes()
                    ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                    ->whereNotIn('sau_users.id', $users_ids)
                    ->whereIn('sau_company_user.company_id', $companies_ids)
                    ->groupBy('id')
                    ->pluck('id', 'name');
        }
                
        return $this->multiSelectFormat($users);
    }

    public function multiselectUsersAutomaticSend()
    {
        $users = User::select(
            "sau_users.id AS id",
            DB::raw("CONCAT(sau_users.document, ' - ', sau_users.name) AS name")
        )
        ->active()
        ->withoutGlobalScopes()
        ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
        ->groupBy('id')
        ->pluck('id', 'name');
                
        return $this->multiSelectFormat($users);
    }

    public function multiselectUsersActionPlan(Request $request)
    {
        $team = $this->team;
        $users = User::selectRaw("
                    sau_users.id as id,
                    sau_users.name as name
                ")->active();

        /*if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                  ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {*/
            $users->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_role_user', function($q) use ($team) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                  ->on('sau_role_user.team_id', '=', DB::raw($team));
            })
            ->leftJoin('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->whereNotIn('sau_roles.id', [8,9]);
        //}

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $users = $users->where(function ($query) use ($keyword) {
                        $query->orWhere('sau_users.name', 'like', $keyword);
                    })->get();
                    //->take(30)->pluck('id', 'name');

            $isSuper = $this->user->hasRole('Superadmin', $this->team);

            if (!$isSuper)
            {
                $users = $users->filter(function ($user, $key) {
                    return !$user->hasRole('Superadmin', $this->team);
                });
            }

            $users = $users->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($users)
            ]);
        }
        else
        {
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
    }

    public function multiselectUsersActionPlanContract(Request $request)
    {
        $users = User::selectRaw("
            sau_users.id as id,
            Concat(sau_users.name, ' - ', sau_ct_information_contract_lessee.social_reason) as name
        ")
        ->active();

        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {      
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_user_information_contract_lessee.information_id')
            ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {
            $users/*->selectRaw("
                sau_users.id as id,
                Concat(sau_users.name, ' - ', sau_ct_information_contract_lessee.social_reason) as name
            ")*/
            //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
            ->Join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_user_information_contract_lessee.information_id')
            ->where('sau_ct_information_contract_lessee.company_id', $this->company);
        }

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $users = $users->where(function ($query) use ($keyword) {
                        $query->orWhere('sau_users.name', 'like', $keyword)                     
                        ->orWhere('sau_ct_information_contract_lessee.social_reason', 'like', $keyword);
                    })->get();
                    //->take(30)->pluck('id', 'name');

            $isSuper = $this->user->hasRole('Superadmin', $this->team);

            if (!$isSuper)
            {
                $users = $users->filter(function ($user, $key) {
                    return !$user->hasRole('Superadmin', $this->team);
                });
            }

            $users = $users->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($users)
            ]);
        }
        else
        {
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
    }

    public function addUserOtherCompany(UserOtherCompanyRequest $request)
    {
        DB::beginTransaction();

        try
        {
            foreach ($request->users as $key => $value)
            {
                $user = User::find($value['user_id']);

                if ($user)
                {
                    $user->companies()->attach($this->company);

                    $roles = $this->getValuesForMultiselect($value['role_id']);
                    $roles = Role::whereIn('id', $roles)->get();

                    if (COUNT($roles) > 0)
                    {
                        $team = Team::where('name', $this->company)->first();
                        $user->attachRoles($roles, $team);
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se agregarón los usuarios'
        ]);
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new UsersImportTemplate(collect([]), $this->company), 'PlantillaImportacionUsuarios.xlsx');
    }

    /**
     * import.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try
      {
        UserImportJob::dispatch($request->file, $request->role_id, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function storeFirm(Request $request)
    {
        $user = User::find($this->user->id);

        $data = $request->all();
          
        if ($request->type == "Dibujar")
        {
            $image_64 = $request->firm_image;
    
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
    
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
    
            $image = str_replace($replace, '', $image_64); 
    
            $image = str_replace(' ', '+', $image); 
    
            $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

            $request->firm_image = base64_decode($image);

            $request->imageName = $imageName;

            if ($request->imageName != $user->firm)
            {
                if ($request->firm_image)
                {
                    $file = $request->firm_image;
                    Storage::disk('s3')->delete('administrative/firms_users/'. $user->id. '/' .$user->firm);
                    Storage::disk('s3')->put('administrative/firms_users/'.$user->id.'/' . $imageName, $file, 'public');
                    //$file->storeAs('administrative/firms_users/'.$user->id.'/' , $imageName, 'public');
                    $user->firm = $imageName;
                    $data['firm_image'] = $imageName;
                    $data['old_firm'] = $imageName;
                    $data['firm_path'] = Storage::disk('s3')->url('administrative/firms_users/'.$user->id.'/' , $imageName);
                }
                else
                {
                    Storage::disk('s3')->delete('administrative/firms_users/'. $user->id. '/' .$user->firm);
                    $user->firm = NUlL;
                    $data['firm_image'] = NULL;
                    $data['old_firm'] = NULL;
                    $data['firm_path'] = NULL;
                }
            }

        }
        else
        {
            if ($request->firm_image != $user->firm)
            {
                if ($request->firm_image)
                {
                    $file = $request->firm_image;
                    Storage::disk('s3')->delete('administrative/firms_users/'. $user->id. '/'. $user->firm);
                    $nameFile = base64_encode($this->user->id . rand(1,10000) . now()) .'.'. $file->extension();
                    $file->storeAs('administrative/firms_users/'. $user->id, $nameFile, 's3');
                    $user->firm = $nameFile;
                    $data['logo'] = $nameFile;
                    $data['old_logo'] = $nameFile;
                    $data['logo_path'] = Storage::disk('s3')->url('administrative/firms_users/'. $user->id. '/'. $nameFile);
                }
                else
                {
                    Storage::disk('s3')->delete('administrative/firms_users/'. $user->id. '/'. $user->firm);
                    $user->firm = NUlL;
                    $data['logo'] = NULL;
                    $data['old_logo'] = NULL;
                    $data['logo_path'] = NULL;
                }
            }
        }

        if (!$user->update())
            return $this->respondHttp500();

        return $this->respondHttp200([
            'message' => 'Se creo la firma'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showFirm()
    {
        try
        {
            $user = User::select('id','firm')->find($this->user->id);
            $user->firm_image = $user->firm;
            $user->old_firm = $user->firm;
            $user->firm_path = Storage::disk('s3')->url('administrative/firms_users/'. $user->id . '/' . $user->firm);
            $user->type = '';

            return $this->respondHttp200([
                'data' => $user
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function userActivities(Request $request)
    {
        $data = LogUserActivitySystem::selectRaw("
            sau_users.name,
            sau_log_activities_users_system.module as module,
            sau_log_activities_users_system.description as description,
            date_format(sau_log_activities_users_system.created_at, '%Y-%m-%d') as date
        ")
        ->join('sau_users', 'sau_users.id', 'sau_log_activities_users_system.user_id')
        ->where('company_id', $this->company);

        if (COUNT($request->get('filters')) > 0)
        {
            $filters = $request->get('filters');

            if (isset($filters['modules']) && COUNT($filters['modules']) > 0)
            {
                if ($filters['filtersType']['modules'] == 'IN')
                    $data->whereIn('sau_log_activities_users_system.module', $this->getValuesForMultiselect($filters["modules"]));
                else
                    $data->whereNotIn('sau_log_activities_users_system.module', $this->getValuesForMultiselect($filters["modules"]));
                
            }

            if (isset($filters['users']) && COUNT($filters['users']) > 0)
            {
                if ($filters['filtersType']['users'] == 'IN')
                    $data->whereIn('sau_log_activities_users_system.user_id', $this->getValuesForMultiselect($filters["users"]));
                else
                    $data->whereNotIn('sau_log_activities_users_system.user_id', $this->getValuesForMultiselect($filters["users"]));
                
            }

            if (isset($filters["dateRange"]) && $filters["dateRange"])
            {
                $dates_request = explode('/', $filters["dateRange"]);

                $dates = [];

                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $data->betweenDate($dates);
            }
        }

        $data2 = LogDelete::selectRaw("
            sau_users.name,
            sau_log_delete.module as module,
            sau_log_delete.description as description,
            date_format(sau_log_delete.created_at, '%Y-%m-%d') as date
        ")
        ->join('sau_users', 'sau_users.id', 'sau_log_delete.user_id')
        ->where('company_id', $this->company);

        if (COUNT($request->get('filters')) > 0)
        {
            $filters = $request->get('filters');

            if (isset($filters['modules']) && COUNT($filters['modules']) > 0)
            {
                if ($filters['filtersType']['modules'] == 'IN')
                    $data2->whereIn('sau_log_delete.module', $this->getValuesForMultiselect($filters["modules"]));
                else
                    $data2->whereNotIn('sau_log_delete.module', $this->getValuesForMultiselect($filters["modules"]));
                
            }

            if (isset($filters['users']) && COUNT($filters['users']) > 0)
            {
                if ($filters['filtersType']['users'] == 'IN')
                    $data2->whereIn('sau_log_delete.user_id', $this->getValuesForMultiselect($filters["users"]));
                else
                    $data2->whereNotIn('sau_log_delete.user_id', $this->getValuesForMultiselect($filters["users"]));
                
            }

            if (isset($filters["dateRange"]) && $filters["dateRange"])
            {
                $dates_request = explode('/', $filters["dateRange"]);

                $dates = [];

                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, $this->formatDateToSave($dates_request[0]));
                    array_push($dates, $this->formatDateToSave($dates_request[1]));
                }
                    
                $data2->betweenDate($dates);
            }
        }

        $data->union($data2)->orderBy('date', 'DESC');

        return Vuetable::of($data)
                ->make();
    }

    public function multiselectModuleActivity()
    {
        $data = LogUserActivitySystem::selectRaw("
            distinct(sau_log_activities_users_system.module) as name
        ")
        ->where('company_id', $this->company);

        $data2 = LogDelete::selectRaw("
            distinct(sau_log_delete.module) as name
        ")
        ->where('company_id', $this->company);

        $data->union($data2);

        $data = $data->pluck('name', 'name');

        return $this->multiSelectFormat($data);

    }
}