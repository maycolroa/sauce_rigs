<?php

use Illuminate\Database\Seeder;
use App\Models\General\Module;
use App\Models\General\Permission;

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

            $exception_permissions = [
                'configurations_u',
                'configurations_d',
                'actionPlans_c',
                'actionPlans_d',
                //'contracts_d',
                'logMails_c',
                'logMails_u',
                'logMails_d',
                'legalMatrix_c',
                'legalMatrix_r',
                'legalMatrix_u',
                'legalMatrix_d',
                'reinstatements_c',
                'reinstatements_r',
                'reinstatements_u',
                'reinstatements_d',
                'logos_u',
                'logos_d',
                'labels_c',
                'labels_d',
                'absenteeism_c',
                'absenteeism_r',
                'absenteeism_u',
                'absenteeism_d',
                'dangerousConditions_c',
                'dangerousConditions_r',
                'dangerousConditions_u',
                'dangerousConditions_d',
                'usersCompanies_c',
                'usersCompanies_u',
                'usersCompanies_d'
            ];

            $modules = Module::all();

            foreach ($modules as $module)
            {
                $module->name = str_replace("/", "_", $module->name);

                foreach ($all_permissions as $permission)
                {
                    $permission_name = $module->name.$permission[0];

                    if (!in_array($permission_name, $exception_permissions))
                    {
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
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase MakeAllPermissionsSeeder '.$e);
        }

    }
}
