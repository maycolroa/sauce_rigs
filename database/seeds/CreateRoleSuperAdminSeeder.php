<?php

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Role;
use App\Models\Permission;

class CreateRoleSuperAdminSeeder extends Seeder
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
            $this->command->info('Comienza el seeder del rol superadmin');

            $module = Module::where('name', 'roles')->first();

            if ($module)
            {
                $role = Role::withoutGlobalScopes()
                    ->updateOrCreate(['name' => 'Superadmin', 'type_role' => 'Definido'], 
                        [
                            'name' => 'Superadmin',
                            'display_name' => 'Superadmin',
                            'description' => 'Rol creado automáticamente con todos los permisos del sistema',
                            'type_role' => 'Definido',
                            'module_id' => $module->id
                        ]);

                if ($role)
                {
                    $ids = Permission::select('id')->get()->pluck('id');

                    $role->syncPermissions($ids);
                }
                else
                    $this->command->info('Rol no creado/encontrado');
            }
            else
                $this->command->info('Modulo por defecto no encontrado');

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase CreateRoleMasterSeeder');
        }
    }
}
