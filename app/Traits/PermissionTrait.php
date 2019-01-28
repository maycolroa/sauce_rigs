<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Administrative\License;
use App\Models\Permission;
use App\Models\Module;
use Exception;

trait PermissionTrait
{
    /**
     * Returns an arrangement with permissions per module
     *
     * @return Array
     */
    public function getModulePermissions()
    {
        //Obtiene los module_id de todas las licencias activas
        $license = License::whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
                ->first();

        $modules = [];

        foreach ($license->modules as $module)
        {
            array_push($modules, $module->id);
        }
        
        //Obtiene todos los permisos de esos module_id
        $permissions = Permission::select("id", "name", "module_id")->whereIn("module_id", $modules)->get();

        $options = [
            'validate_all' => true,
            'return_type' => 'both'
        ];

        //Devuelve un array con true/false para cada verificacion de permiso para el usuario en sesion
        list($validate, $allValidations) = Auth::user()->ability(
            null,
            array_keys($permissions->pluck('id', 'name')->toArray()),
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
}