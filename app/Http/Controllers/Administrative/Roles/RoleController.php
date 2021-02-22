<?php

namespace App\Http\Controllers\Administrative\Roles;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Roles\RoleRequest;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Permission;

class RoleController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:roles_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:roles_r, {$this->team}", ['except' =>['multiselect', 'multiselectPermissions']]);
        $this->middleware("permission:roles_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:roles_d, {$this->team}", ['only' => 'destroy']);
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
        //if ($this->user->can('roles_manage_defined', $this->team))

            $roles = Role::select(
                'sau_roles.id as id',
                'sau_roles.name as name',
                'sau_roles.description as description',
                'sau_roles.type_role as type_role',
                'sau_roles.company_id as company_id',
                'sau_modules.display_name as display_name'
            )
            ->leftJoin('sau_modules', 'sau_modules.id', 'sau_roles.module_id')
            ->alls();
        /*else 
        
            $roles = Role::select(
                'sau_roles.id as id',
                'sau_roles.name as name',
                'sau_roles.description as description'
            )
            ->company();*/

       return Vuetable::of($roles)
            ->addColumn('administrative-roles-edit', function ($role) {
                if (!$role->company_id)
                {
                    if ($this->user->can('roles_manage_defined', $this->team))
                        return true;
                    else 
                        return false;
                }

                return true;
            })
            ->addColumn('control_delete', function ($role) {
                if (!$role->company_id)
                {
                    if ($this->user->can('roles_manage_defined', $this->team))
                        return true;
                    else 
                        return false;
                }

                return true;
            })
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
        $role = new Role();
        $role->name = $request->get('name');
        $role->display_name = $request->get('name');
        $role->description = $request->get('description');

        if ($request->has('type_role') && $request->get('type_role') == 'Definido')
        {
            $role->type_role = 'Definido';
            $role->module_id = $request->get('module_id');
        }
        else
            $role->company_id = $this->company;
        
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
    public function show(Role $role)
    {
        try
        {
            if ($role->module)
                $role->multiselect_module = $role->module->multiselect();

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
        //$role = Role::conditionController()->findOrFail($id);

        $role->name = $request->get('name');
        $role->display_name = $request->get('name');
        $role->description = $request->get('description');

        if ($request->has('type_role') && $request->get('type_role') == 'Definido')
            $role->module_id = $request->get('module_id');
        
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
        //$role = Role::conditionController()->findOrFail($id);

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
        $includeSuper = $this->user->hasRole('Superadmin', $this->team) ? true : false;

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $roles = Role::form($includeSuper)->select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($roles)
            ]);
        }
        else
        {
            $roles = Role::alls($includeSuper)->selectRaw(
               "sau_roles.id as id,
                sau_roles.name as name")
                ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($roles);

        }
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
            $ids = array_keys($value);
            $data = Permission::whereIn('id', $ids)->pluck('name', 'display_name');
            $permissions[$key] = $this->multiSelectFormat($data);
        }
        
        return $permissions;
    }

    public function permissionsMultiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $roles = Permission::form($includeSuper)->select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($roles)
            ]);
        }
        else
        {
            $roles = Permission::selectRaw(
               "sau_permissions.id as id,
                sau_permissions.description as name")
                ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($roles);

        }
    }
}
