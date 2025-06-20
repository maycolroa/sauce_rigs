<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\System\Licenses\License;
use App\Models\General\Permission;
use App\Models\General\Application;
use App\Models\General\Module;
use App\Models\General\Team;
use Exception;
use Session;

trait PermissionTrait
{
    /**
     * Returns an arrangement with permissions per module
     *
     * @return Array
     */
    public function getModulePermissions($company = null, $user = null)
    {
        //Equipo donde se debe realizar la verificacion de permisos
        $company = $company ? $company : Session::get('company_id');
        $user = $user ? $user : Auth::user();
        $team = Team::where('name', $company)->first();

        //Obtiene los module_id de todas las licencias activas
        $licenses = License::whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])->get();

        $modules = [];

        foreach ($licenses as $license)
        {
            foreach ($license->modules as $module)
            {
                array_push($modules, $module->id);
            }
        }

        if ($user->hasRole('Superadmin', $team))
        {
            $app = Application::where('name', 'system')->first();

            if ($app)
            {
                foreach ($app->modules as $module)
                {
                    array_push($modules, $module->id);
                }
            }
        }
        
        //Obtiene todos los permisos de esos module_id
        $permissions = Permission::select("id", "name", "module_id")->whereIn("module_id", $modules)->get();

        $options = [
            'validate_all' => true,
            'return_type' => 'both'
        ];

        //Devuelve un array con true/false para cada verificacion de permiso para el usuario en sesion
        list($validate, $allValidations) = $user->ability(
            null,
            array_keys($permissions->pluck('id', 'name')->toArray()),
            $team,
            $options
        );

        $allValidations = $allValidations["permissions"];

        //Filtra de todos los permisos obtenidos cuales son los permitidos
        $data = $permissions->pluck('id', 'name')->filter(function ($value, $key) use ($allValidations) {
            return $allValidations[$key];
        })->toArray();

        $final = [];

        foreach ($permissions as $value)
        {
            if (isset($data[$value->name]))
                $final[$value->module_id][$value->id] = $value->name;
        }

        return $final;
    }

    /**
     * Returns an object with the applications and modules permissions 
     * according to the active licenses for the user in session
     *
     * @return array
     */
    public function getAppsModules()
    {
        $permissions = $this->getModulePermissions();
        $data = [];
        
        if (COUNT($permissions) > 0)
        {
            $module_ids = array_keys($permissions);

            $modules = Module::whereIn('id',$module_ids)->get();
            $arr_sub_mod = [];

            foreach ($modules as $mod)
            {
                $app = $mod->application;
                $app->name = strtolower($app->name);
                $mod->name = strtolower($mod->name);
                $arr_mod = [];

                if (!isset($data[$app->name]))
                {
                    $data[$app->name]["id"]           = $app->id;
                    $data[$app->name]["display_name"] = $app->display_name;
                    $data[$app->name]["image"]        = $app->image;
                    $data[$app->name]["banner"]       = $this->checkBanner($app->image);
                    $data[$app->name]["modules"]      = [];
                }

                $subMod_name = explode("/", $mod->name);
                $subMod_display_name = explode("/", $mod->display_name);

                if (COUNT($subMod_name) == 1) //Modulo
                {
                    array_push($data[$app->name]["modules"], ["id"=>$mod->id, "name"=>$mod->name, "display_name"=>$mod->display_name]);
                }
                else //Submodulo
                {
                    if (!isset($arr_sub_mod[$app->name][$subMod_name[0]]))
                    {
                        array_push($data[$app->name]["modules"], ["name"=>$subMod_name[0], "display_name"=>$subMod_display_name[0], "subModules"=>[]]);
                    }

                    $arr_sub_mod[$app->name][$subMod_name[0]][] = [
                        "id"=>$mod->id, "name"=> $subMod_name[1], "display_name" => $subMod_display_name[1]
                    ];
                }

                foreach ($data as $keyApp => $value)
                {
                    foreach ($value["modules"] as $keyMod => $value2)
                    {
                        if (isset($value2["subModules"]))
                        {
                            $data[$keyApp]["modules"][$keyMod]["subModules"] = $arr_sub_mod[$keyApp][$value2["name"]];
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getIdsModulePermissions()
    {
        $permissions = $this->getModulePermissions();
        $data = [];
        
        if (COUNT($permissions) > 0)
        {
            $data = array_keys($permissions);
        }

        return $data;
    }

    /**
     * Returns an object with the applications and modules permissions 
     * according to the active licenses for the user in session
     *
     * @return array
     */
    public function getLicenseAppsModules()
    {
        $modules = Module::main()->get();
        $arr_sub_mod = [];

        foreach ($modules as $mod)
        {
            $app = $mod->application;
            $app->name = strtolower($app->name);
            $mod->name = strtolower($mod->name);
            $arr_mod = [];

            if (!isset($data[$app->name]))
            {
                $data[$app->name]["id"]           = $app->id;
                $data[$app->name]["display_name"] = $app->display_name;
                $data[$app->name]["image"]        = $app->image;
                $data[$app->name]["banner"]       = $this->checkBanner($app->image);
                $data[$app->name]["modules"]      = []; 
            }

            $subMod_name = explode("/", $mod->name);
            $subMod_display_name = explode("/", $mod->display_name);

            if (COUNT($subMod_name) == 1) //Modulo
            {
                array_push($data[$app->name]["modules"], ["id"=>$mod->id, "name"=>$mod->name, "display_name"=>$mod->display_name]);
            }
            else //Submodulo
            {
                if (!isset($arr_sub_mod[$app->name][$subMod_name[0]]))
                {
                    array_push($data[$app->name]["modules"], ["name"=>$subMod_name[0], "display_name"=>$subMod_display_name[0], "subModules"=>[]]);
                }

                $arr_sub_mod[$app->name][$subMod_name[0]][] = [
                    "id"=>$mod->id, "name"=> $subMod_name[1], "display_name" => $subMod_display_name[1]
                ];
            }

            foreach ($data as $keyApp => $value)
            {
                foreach ($value["modules"] as $keyMod => $value2)
                {
                    if (isset($value2["subModules"]))
                    {
                        $data[$keyApp]["modules"][$keyMod]["subModules"] = $arr_sub_mod[$keyApp][$value2["name"]];
                    }
                }
            }
        }

        return $data;
    }

    public function checkBanner($image)
    {
        if (file_exists( public_path() . "/images/{$image}_banner.png")) {
            return "/images/{$image}_banner.png";
        } else {
            return null;
        }     
    }

    /**
     * Returns true if the company that sends the mail has an active license 
     * for the module from which the request is made
     *
     * @return Booleam
     */
    private function checkLicense($company_id, $module_id)
    {
        $licenses = License::
              join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', $module_id);

        $licenses->company_scope = $company_id;

        return $licenses->exists();
    }

    /**
     * Returns true if the company that sends the mail has an active license 
     * for the module from which the request is made
     *
     * @return Booleam
     */
    private function checkRolePermissionInModule($role_id, $module_id)
    {
        $permissions = Permission::
              join('sau_permission_role', 'sau_permission_role.permission_id', 'sau_permissions.id')
            ->where('sau_permissions.module_id', $module_id)
            ->where('sau_permission_role.role_id', $role_id);

        return $permissions->exists();
    }
}