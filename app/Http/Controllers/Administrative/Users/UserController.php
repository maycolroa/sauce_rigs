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
use App\Traits\UserTrait;
use App\Traits\ContractTrait;
use App\Jobs\Administrative\Users\UserExportJob;
use Session;
use Validator;
use Hash;
use Illuminate\Support\Facades\Auth;
use DB;

class UserController extends Controller
{
    use UserTrait;
    use ContractTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users_c', ['only' => 'store']);
        $this->middleware('permission:users_r', ['except' =>'multiselect']);
        $this->middleware('permission:users_u', ['only' => 'update']);
        //$this->middleware('permission:users_d', ['only' => 'destroy']);
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
        if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
        {
            $users = User::select('sau_users.*')
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser(Auth::user()->id));
        }
        else
        {
            $users = User::select(
                'sau_users.*',
                'sau_roles.name as role'
            )
            ->withoutGlobalScopes()
            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->where('sau_company_user.company_id', Session::get('company_id'))
            ->whereRaw('(sau_roles.company_id = '.Session::get('company_id').' OR (sau_roles.company_id IS NULL AND sau_roles.name IN("Contratista", "Arrendatario") ) )');
        }

       return Vuetable::of($users)
                ->addColumn('administrative-users-edit', function ($user) {
                    return $user->id != Auth::user()->id; 
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
        $user = $this->createUser($request);
 
        if ($user == $this->respondHttp500() || $user == null) {
            return $this->respondHttp500();
        }

        if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
        {
            $user->syncRoles([Auth::user()->roleUser[0]]);
            $contract = $this->getContractUser(Auth::user()->id);
            $contract->users()->attach($user);
        }
        else
            $user->syncRoles([$request->get('role_id')]);

        return $this->respondHttp200([
            'message' => 'Se creo el usuario'
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
        $user = User::findOrFail($id);
            
        try
        {
            $role = Role::withoutGlobalScopes()
                ->join('sau_role_user', 'sau_role_user.role_id', 'sau_roles.id')
                ->where('sau_roles.company_id', Session::get('company_id'))
                ->where('sau_role_user.user_id', $user->id)
                ->first();

            if ($role)
            {
                $user->multiselect_role = $role->multiselect();
                $user->role_id = $role->id;
                $user->old_role_id = $role->id;
            }

            if (!$user->role_id)
                $user->edit_role = false;
            else
                $user->edit_role = true;
            
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
        $user->fill($request->all());
        
        if(!$user->update()){
          return $this->respondHttp500();
        }

        if (!Auth::user()->hasRole('Arrendatario') && !Auth::user()->hasRole('Contratista'))
        {
            if ($request->get('edit_role') == 'true')
            {
                $user->roles()->detach($request->get('old_role_id'));
                $user->roles()->attach($request->get('role_id'));
            }
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el usuario'
        ]);
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
            UserExportJob::dispatch(Auth::user(), Session::get('company_id'));
          
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

        if (Auth::user()->hasRole('Arrendatario') || Auth::user()->hasRole('Contratista'))
        {
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                  ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser(Auth::user()->id));
        }
        else
        {
            $users->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
                  ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id');
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
                    if(!Hash::check($value, Auth::User()->password))
                        $fail('La contraseña actual no coincide con la registrada en el sistema');
                },
            ]
        ])->validate();

        Auth::user()->fill($request->all());
        
        if(!Auth::user()->update()){
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
                    "module_id" => Auth::user()->module_id,
                    "multiselect_module" => Auth::user()->defaultModule ? Auth::user()->defaultModule->multiselect() : ''
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

        Auth::user()->default_module_url = $module->application->name.'/'.$module->name;
        Auth::user()->module_id = $module->id;

        if(!Auth::user()->update()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo su módulo favorito'
        ]);
    }
}