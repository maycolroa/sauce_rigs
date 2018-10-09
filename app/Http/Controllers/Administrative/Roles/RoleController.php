<?php

namespace App\Http\Controllers\Administrative\Roles;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Roles\RoleRequest;
use App\Models\Role;
use App\Models\Permission;
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

        $permissions = [];

        foreach($request->get('permissions_asignates') as $v)
        {
            $item = json_decode($v)->permissions;

            foreach ($item as $key => $value)
            {
                array_push($permissions, $value->value);
            }
        }

        $ids = Permission::select('id')->whereIn("name", $permissions)
                        ->get()
                        ->pluck('id');

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
            $data = [];

            foreach ($permissions as $key => $value)
            {
                if (!isset($data[$value->module->id]))
                {
                    $arr = [
                        'id' => $value->module->id,
                        'name' => $value->module->display_name,
                        'permissions' => []
                    ];

                    $data[$value->module->id] = $arr;
                }

                array_push($data[$value->module->id]["permissions"], $value->multiselect());
            }

            $role->permissions_asignates = $data;

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

        $permissions = [];

        foreach($request->get('permissions_asignates') as $v)
        {
            $item = json_decode($v)->permissions;

            foreach ($item as $key => $value)
            {
                array_push($permissions, $value->value);
            }
        }

        $ids = Permission::select('id')->whereIn("name", $permissions)
                        ->get()
                        ->pluck('id');

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

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */
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

    /**
     * Returns an array for a select type input
     *
     * @return void
     */
    public function multiselectPermissions()
    {
        $permissions = $this->getModulePermissions();

        foreach ($permissions as $key => $value)
        {
            $permissions[$key] = $this->multiSelectFormat($value);
        }
        
        return $permissions;
    }
}
