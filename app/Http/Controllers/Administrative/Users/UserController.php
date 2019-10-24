<?php

namespace App\Http\Controllers\Administrative\Users;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Users\UserRequest;
use App\Http\Requests\Administrative\Users\ChangePasswordRequest;
use App\Http\Requests\Administrative\Users\DefaultModuleRequest;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Module;
use App\Models\General\Team;
use App\Traits\UserTrait;
use App\Traits\ContractTrait;
use App\Traits\PermissionTrait;
use App\Jobs\Administrative\Users\UserExportJob;
use Validator;
use Hash;
use DB;

class UserController extends Controller
{
    use UserTrait;
    use ContractTrait;
    use PermissionTrait;

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
        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
        {
            $users = User::select(
                    'sau_users.id AS id',
                    'sau_users.name AS name',
                    'sau_users.email AS email',
                    'sau_users.document AS document',
                    'sau_users.document_type AS document_type',
                    'sau_users.active AS active'
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
                      ->orWhereNull('sau_role_user.role_id');
        }

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

            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $role = $this->user->hasRole('Arrendatario', $this->team) ? 'Arrendatario' : 'Contratista';
                $role = Role::defined()->where($role)->first();

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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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

            $user->multiselect_filter_system_apply = $systemsApply;
            $user->filter_system_apply = $systemsApply;
            
            return $this->respondHttp200([
                'data' => $user,
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
            $user->fill($request->all());
            
            if (!$user->update())
                return $this->respondHttp500();

            if (!$user->hasRole('Arrendatario', $this->team) || !$user->hasRole('Contratista', $this->team))
            {
                if ($request->get('edit_role') == 'true')
                {
                    $roles = $this->getDataFromMultiselect($request->role_id);
                    $user->syncRoles($roles, $this->team);
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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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
        /*DB::beginTransaction();

        try
        {
            if (count($user->actionPlanResponsibles) > 0 || count($user->actionPlanCreator) > 0)
            {
                return $this->respondWithError('No se puede eliminar el usuario porque hay registros asociados a él');
            }

            //$user->companies()->detach();
            $user->syncRoles([]); // Eliminar datos de relaciones
            $user->syncPermissions([]); // Eliminar datos de relaciones
            //$user->contractInformation()->detach(); // Eliminar relación de contratista o arrendatario

            if(!$user->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();
            
            return $this->respondHttp200([
                'message' => 'Se elimino el usuario'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return $this->respondHttp500();
        }*/
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        try
        {
            UserExportJob::dispatch($this->user, $this->company);
          
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
                    })
                    ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($users)
            ]);
        }
        else
        {
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
                    "multiselect_module" => $this->user->defaultModule ? $this->user->defaultModule->multiselect() : ''
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
        $module = Module::findOrFail($request->module_id);

        $this->user->default_module_url = $module->application->name.'/'.$module->name;
        $this->user->module_id = $module->id;

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
        $modules = Module::whereIn('name', ['legalMatrix', 'reinstatements'])->get();
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
}