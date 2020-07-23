<?php

namespace App\Services;

use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\System\Licenses\License;
use App\Models\General\Application;
use App\Models\General\Module;
use App\Models\General\Permission;
use App\Models\General\Team;
use Exception;

class PermissionService
{
    /**
     * Devuelve una coleccion con las apps, modulos y permisos 
     * que tiene el usuario indicado para la empresa enviada
     *
     * @param App\Models\Administrative\User $user
     * @param int $company
     * @return collect
     */
    public function getAppModulesPermission($user, $company)
    {
        $team = Team::where('name', $company)->first();
        $query = Application::get();
        $apps = collect([]);

        $query->each(function($appIter, $keyApp) use (&$apps, $user, $team, $company) {
            $app = collect([]);
            $app->put('id', $appIter->id);
            $app->put('name', strtolower($appIter->name));
            $app->put('display_name', $appIter->display_name);
            $app->put('image', $appIter->image);
            $app->put('banner', $this->checkBanner($appIter->image));

            $modules = collect([]);

            $appIter->modules->each(function($moduleIter, $keyMod) use (&$modules, $user, $team) {
                $module = collect([]);
                $module->put('id', $moduleIter->id);
                $module->put('name', strtolower($moduleIter->name));
                $module->put('display_name', $moduleIter->display_name);
                $module->put('logo', $moduleIter->logo);
                $module->put('main', $moduleIter->main == 'SI' ? true : false);
                $module->put('isSubModule', COUNT(explode("/", $moduleIter->name)) == 2 ? true : false);
                $module->put('mainSubModule', strtolower(explode("/", $moduleIter->name)[0]));
                $module->put('parentApp', $moduleIter->application->name);

                $permissions = collect([]);

                $moduleIter->permissions->each(function($perIter, $keyPer) use (&$permissions, $user, $team) {
                    $permission = collect([]);
                    $permission->put('id', $perIter->id);
                    $permission->put('name', $perIter->name);
                    $permission->put('display_name', $perIter->display_name);
                    $permission->put('description', $perIter->description);
                    $permission->put('can', $user->can($perIter->name, $team));
                    $permissions->push($permission);
                });

                $module->put('permissions', $permissions);
                $modules->push($module);
            });
            
            $modules = $modules->filter(function($module, $keyMod) use ($company, $user, $team) {
                if ($module->get('parentApp') == 'system' && $user->isSuperAdmin($team))
                    return true;

                return $this->existsLicenseByModule($company, $module->get('id')) && 
                ($user->isSuperAdmin($team) || $module->get('permissions')->where('can', true)->first());
            })
            ->values();

            $app->put('modules', $modules);
            $apps->push($app);
        });

        $apps = $apps->filter(function($app, $keyApp) {
            return COUNT($app->get('modules')) > 0;
        })
        ->values();
        //\Log::info($apps);

        return $apps;
    }

    /**
     * Verifica si existe una imagen almacenada para el modulo
     *
     * @param string $image
     * @return string|null
     */
    public function checkBanner($image)
    {
        if (file_exists( public_path() . "/images/{$image}_banner.png")) {
            return "/images/{$image}_banner.png";
        } else {
            return null;
        }     
    }

    /**
     * Obtiene las licencias activas para una compañia
     *
     * @param int $company_id
     * @return Booleam
     */
    public function getLicenses($company_id)
    {
        $licenses = License::whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')]);
        $licenses->company_scope = $company_id;

        return $licenses->get();
    }

    /**
     * Verifica si existe una licencia valida para una compañia
     * en el modulo indicado
     *
     * @param int $company_id
     * @param int $module_id
     * @return Booleam
     */
    public function existsLicenseByModule($company_id, $module_id)
    {
        $licenses = License::
              join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', $module_id);

        $licenses->company_scope = $company_id;

        return $licenses->exists();
    }

    /**
     * Devuelve una coleccion (con el formato del menu) 
     * con las apps, modulos y permisos que tiene el usuario 
     * indicado para la empresa enviada
     *
     * @param App\Models\Administrative\User $user
     * @param int $company
     * @return collect
     */
    public function getModulesFormatVue($user, $company)
    {
        $apps = $this->getAppModulesPermission($user, $company);

        $apps = $apps->mapWithKeys(function($app, $keyApp) {
            $modulesGroup = $app->get('modules')->groupBy('mainSubModule');
            $modules = collect([]);

            $modulesGroup->each(function($module, $keyMod) use (&$modules) {
                $module = $module->transform(function($item, $key) {
                    return $item->except('permissions');
                });

                if ($module->count() == 1)
                {
                    $modules->push($module->first());
                }
                else
                {
                    $new = collect([]);
                    $new->put('name', $keyMod);
                    $new->put('display_name', explode("/", $module->first()->get('display_name'))[0]);
                    $new->put('subModules', $module->transform(function($subModule, $keySub) {
                        $subModule->put('name', explode("/", $subModule->get('name'))[1]);
                        $subModule->put('display_name', explode("/", $subModule->get('display_name'))[1]);
                        return $subModule;
                    }));

                    $modules->push($new);
                }
            });

            $app->put('modules', $modules);

            return [$app['name'] => $app];
        });

        return $apps;
    }

    /**
     * Devuelve una coleccion con los roles definidos
     * indicando si el usuario lo tiene o no
     *
     * @param App\Models\Administrative\User $user
     * @param int $company
     * @return collect
     */
    public function getHasRole($user, $company)
    {
        $team = Team::where('name', $company)->first();
        $roles = collect([]);

        foreach (Role::defined()->get() as $role)
        {
            $roles->put($role->name, $user->hasRole($role->name, $team));
        }

        return $roles;
    }

    /**
     * Devuelve una coleccion con los permisos
     * indicando si el usuario lo tiene o no
     *
     * @param App\Models\Administrative\User $user
     * @param int $company
     * @return collect
     */
    public function getCan($user, $company)
    {
        $apps = $this->getAppModulesPermission($user, $company);
        $permission_enabled = collect([]);

        $apps->each(function($app, $keyApp) use (&$permission_enabled) {
            $app->get('modules')->each(function($module, $keyMod) use (&$permission_enabled) {
                $module->get('permissions')->each(function($permission, $keyPer) use (&$permission_enabled) {
                    $permission_enabled->push($permission);
                });
            });
        });

        $permissions = collect([]);
        
        foreach (Permission::all() as $permission)
        {
            if ($permission_enabled->contains('name', $permission->name))
                $permissions->put($permission->name, true);
            else
                $permissions->put($permission->name, false);
        }

        return $permissions;
    }

    /**
     * Devuelve todas las compañias activas del usuario en sesion
     *
     * @param App\Models\Administrative\User $user
     * @return collect
     */
    public function getCompaniesActive($user)
    {
        $companies = $user->companies()->withoutGlobalScopes()->get();

        $data = collect([]);

        foreach ($companies as $company)
        {
            if ($company->isActive())
            {
                $licenses = $this->getLicenses($company->id);

                if ($licenses->count() > 0)
                {
                    $data->put($company->id, [
                        'id'    =>  $company->id, 
                        'name'  =>  ucwords(strtolower($company->name))
                    ]);
                }
            }
        }

        return $data;
    }

    /**
     * Devuelve una instancia de modulo por su nombre
     *
     * @param string $name
     * @return App\Models\General\Module
     */
    public function getModuleByName($name)
    {
        return Module::where('name', $name)->firstOrFail();
    }
}