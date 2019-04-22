<?php

use Illuminate\Database\Seeder;
use App\Models\General\Application;
use App\Models\General\Module;
use App\Models\Administrative\Roles\Role;

class MakeCustomRolesDefinedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try
        {  
            $this->command->info('Comienza el seeder de los roles definidos');

            $file = dirname(__FILE__) . '/data/customRolesDefined.json';
            $file = file_get_contents($file);
            $applications = json_decode($file, true);

            foreach ($applications as $value)
            {
                if (isset($value['application']) && isset($value['modules']))
                {
                    $app = Application::where("name", $value["application"])->first();

                    if ($app)
                    {
                        foreach ($value['modules'] as $item)
                        {
                            if (isset($item["name"]) && isset($item["roles"]))
                            {
                                $mod = Module::where("application_id", $app->id)
                                        ->where("name", $item["name"])->first(); 

                                if ($mod)
                                {
                                    foreach ($item['roles'] as $role)
                                    {
                                        if (isset($role["name"]) && isset($role["display_name"]))
                                        {   
                                            $role_exist = Role::withoutGlobalScopes()->where('name', $role["name"])->where('type_role', 'Definido')->where('module_id', $mod->id)->first();

                                            if (!$role_exist)
                                            {
                                                Role::create([
                                                    'name' => $role['name'],
                                                    'display_name' => $role['display_name'],
                                                    'description' => $role['description'],
                                                    'type_role' => 'Definido',
                                                    'module_id' => $mod->id
                                                ]);
                                            }
                                            else
                                                $this->command->info("Rol ".$role['name']." ya existe");
                                        }
                                        else
                                            $this->command->info('Elemento omitido por formato invalido: '. json_encode($permission));
                                    }
                                }
                                else 
                                    $this->command->info("Modulo ".$value["name"]." no encontrado");
                            }
                            else
                                $this->command->info('Elemento omitido por formato invalido: '. json_encode($item));
                        }
                    }
                    else
                        $this->command->info("Aplicacion ".$value["application"]." no encontrada");
                }
                else
                {
                    $this->command->info('Elemento omitido por formato invalido: '. json_encode($value));
                }
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase MakeCustomRolesDefinedSeeder '.$e);
        }
    }
}
