<?php

namespace App\Http\Controllers\Administrative\Roles;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Administrative\Roles\RoleRequest;
use App\Models\Role;
use App\Models\Permission;
use App\Administrative\License;
use Session;

class RoleController extends Controller
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
    
       $roles = Role::select('*');

       return Vuetable::of($roles)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = new Role($request->all());
        $role->company_id = Session::get('company_id');
        
        if(!$role->save())
        {
            return $this->respondHttp500();
        }

        $ids = [];

        foreach($request->get('permissions_multiselect') as $v)
        {
            array_push($ids, json_decode($v)->value);
        }

        $role->syncPermissions($ids); 

        return $this->respondHttp200([
            'message' => 'Se creo el rol'
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
        $role = Role::findOrFail($id);
            
        try
        {
            $permissions = $role->permissions;
            $multiselect = [];

            foreach ($permissions as $key => $value) {
                $multiselect[] = $value->multiselect();
            }

            $role->permissions_multiselect = $multiselect;

            return $this->respondHttp200([
                'data' => $role,
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
    public function update(RoleRequest $request, Role $role)
    {
        $role->fill($request->all());
        
        if(!$role->update()) {            
            return $this->respondHttp500();
        }

        $ids = [];

        foreach($request->get('permissions_multiselect') as $v)
        {
            array_push($ids, json_decode($v)->value);
        }

        $role->syncPermissions($ids);

        return $this->respondHttp200([
            'message' => 'Se actualizo el rol'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->users()->sync([]); // Eliminar datos de relaciones
        $role->permissions()->sync([]); // Eliminar datos de relaciones

        // Ahora forzar la eliminación funcionará independientemente de si 
        //la tabla dinámica tiene eliminación en cascada
        if(!$role->forceDelete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el rol'
        ]);
    }

    public function multiselect(Request $request)
    {
        $keyword = "%{$request->keyword}%";
        $roles = Role::select("id", "name")
            ->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', $keyword);
             })
            ->take(30)->pluck('id', 'name');

        return $this->respondHttp200([
            'options' => $this->multiSelectFormat($roles)
        ]);
    }

    public function multiselectPermissions()
    {
        $licenses = License::select('module_id')->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
                        ->groupBy('module_id')
                        ->get()
                        ->pluck('module_id');

        $permissions = Permission::select("id", "name")->whereIn("module_id", $licenses)->pluck('id', 'name');

        $options = [
            'validate_all' => true,
            'return_type' => 'both'
        ];

        list($validate, $allValidations) = Auth::user()->ability(
            null,
            array_keys($permissions->toArray()),
            $options
        );

        $allValidations = $allValidations["permissions"];

        $data = $permissions->filter(function ($value, $key) use ($allValidations) {
            return $allValidations[$key];
        });

        return $this->multiSelectFormat($data);
    }
}
