<?php

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Module;
use App\Models\Permission;

class MakeCustomPermissionsSeeder extends Seeder
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
            $this->command->info('Comienza el seeder de los permisos personalizados');

            $file = dirname(__FILE__) . '/data/customPemissions.json';
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
                            if (isset($item["name"]) && isset($item["permissions"]))
                            {
                                $mod = Module::where("application_id", $app->id)
                                        ->where("name", $item["name"])->first(); 

                                if ($mod)
                                {
                                    foreach ($item['permissions'] as $permission)
                                    {
                                        if (isset($permission["name"]) && isset($permission["display_name"]))
                                        {   
                                            $permission_exist = Permission::where('name', $permission["name"])->where('module_id', $mod->id)->first();

                                            if (!$permission_exist)
                                            {
                                                Permission::create([
                                                    'name' => $permission['name'],
                                                    'display_name' => $permission['display_name'],
                                                    'description' => $permission['display_name'],
                                                    'module_id' => $mod->id
                                                ]);
                                            }
                                            else
                                                $this->command->info("Permiso ".$permission['name']." ya existe");
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
            $this->command->info('Ocurrio un error al ejecutar la clase MakeCustomPermissionsSeeder '.$e);
        }
    }
}
