<?php

namespace App\Http\Controllers\Administrative\Users;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Users\UserRequest;
use App\Models\Administrative\Users\User;
use App\Traits\UserTrait;
use App\Jobs\Administrative\Users\UserExportJob;
use Session;
use Illuminate\Support\Facades\Auth;
use DB;

class UserController extends Controller
{
    use UserTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users_c', ['only' => 'store']);
        $this->middleware('permission:users_r', ['except' =>'multiselect']);
        $this->middleware('permission:users_u', ['only' => 'update']);
        $this->middleware('permission:users_d', ['only' => 'destroy']);
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
        $users = User::select(
            'sau_users.*',
            'sau_roles.name as role'
        )
        ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
        ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
        ->where('sau_users.id', '<>', Auth::user()->id);

       return Vuetable::of($users)
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

        //$user->companies()->sync(Session::get('company_id'));
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
            $roles = $user->roles;

            foreach ($roles as $key => $value) {
                $user->multiselect_role = $value->multiselect();
                $user->role_id = $value->id;
                break;
            }
            
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

        $user->syncRoles([$request->get('role_id')]);
        
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
        DB::beginTransaction();

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
        }
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

    public function multiselect(Request $request){
        return $this->getUsers($request);
    }

    public function multiselectAll(Request $request){
        return $this->getUsers($request, true);
    }

    private function getUsers($request, $all = false)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $users = User::selectRaw("
                sau_users.id as id,
                CONCAT(sau_users.document, ' - ', sau_users.name) as name
            ")
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->where(function ($query) use ($keyword) {
                $query->orWhere('sau_users.document', 'like', $keyword)
                ->orWhere('sau_users.name', 'like', $keyword);
            });

            if (!$all)
                $users->where('sau_users.id', '<>', Auth::user()->id);

            $users = $users->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($users)
            ]);
        }
        else
        {
            $users = User::selectRaw("
                sau_users.id as id,
                CONCAT(sau_users.document, ' - ', sau_users.name) as name
            ")
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id');

            if (!$all)
                $users->where('sau_users.id', '<>', Auth::user()->id);

            $users = $users->pluck('id', 'name');
            
            return $this->multiSelectFormat($users);
        }
    }
}