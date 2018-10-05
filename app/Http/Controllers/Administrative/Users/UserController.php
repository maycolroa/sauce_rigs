<?php

namespace App\Http\Controllers\Administrative\Users;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Users\UserRequest;
use App\User;
use App\Jobs\Administrative\Users\UserExportJob;
use Session;

class UserController extends Controller
{
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
            'sau_users.name as name', 'sau_users.email as email', 'sau_users.document as document', 
            'sau_users.document_type as document_type', 'roles.name as role_name')
            ->join('sau_company_user','sau_company_user.user_id','sau_users.id')
            ->leftJoin('role_user','role_user.user_id','sau_users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->where('sau_company_user.company_id', Session::get('company_id'));

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
        $user = new User($request->all());
        
        if(!$user->save()){
            return $this->respondHttp500();
        }

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
        $user->companies()->detach();
        $user->syncRoles()->sync([]); // Eliminar datos de relaciones
        $user->syncPermissions()->sync([]); // Eliminar datos de relaciones

        if(!$user->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el usuario'
        ]);
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
            UserExportJob::dispatch();
          
            return $this->respondHttp200();
        } 
        catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}