<?php

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;

class MakeAllPermissionsSeeder extends Seeder
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

            $all_permissions = [
                ['_c', ' - crear'],
                ['_r', ' - ver'],
                ['_u', ' - editar'],
                ['_d', ' - eliminar']
            ];

            $modules = Module::all();

            foreach ($modules as $module)
            {
                $module->name = str_replace("/", "_", $module->name);

                foreach ($all_permissions as $permission)
                {
                    $permission_name = $module->name.$permission[0];

                    $permission_exist = Permission::where('name', $permission_name)->first();

                    if (!$permission_exist)
                    {
                        Permission::create([
                            'name' => $permission_name,
                            'display_name' => $module->display_name.$permission[1],
                            'description' => $module->display_name.$permission[1],
                            'module_id' => $module->id
                        ]);
                    }
                    else
                    {
                        $this->command->info("Permiso $permission_name ya existe");
                    }
                }
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase MakeAllPermissionsSeeder');
        }

    }
}
