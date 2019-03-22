<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameModuleConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sau_modules')
            ->where('name','configurations/locationLevelForm')
            ->update([
                'name' => 'configurations',
                'display_name' => 'Configuraciones'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_locationLevelForm_c')
            ->update([
                'name' => 'configurations_c',
                'display_name' => 'Configuraciones - crear',
                'description' => 'Configuraciones - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_locationLevelForm_r')
            ->update([
                'name' => 'configurations_r',
                'display_name' => 'Configuraciones - ver',
                'description' => 'Configuraciones - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_locationLevelForm_u')
            ->update([
                'name' => 'configurations_u',
                'display_name' => 'Configuraciones - editar',
                'description' => 'Configuraciones - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_locationLevelForm_d')
            ->update([
                'name' => 'configurations_d',
                'display_name' => 'Configuraciones - eliminar',
                'description' => 'Configuraciones - eliminar'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sau_modules')
            ->where('name','configurations')
            ->update([
                'name' => 'configurations/locationLevelForm',
                'display_name' => 'Configuraciones/Nivel localización en formulario'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_c')
            ->update([
                'name' => 'configurations_locationLevelForm_c',
                'display_name' => 'Nivel de locación en formulario - crear',
                'description' => 'Nivel de locación en formulario - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_r')
            ->update([
                'name' => 'configurations_locationLevelForm_r',
                'display_name' => 'Nivel de locación en formulario - ver',
                'description' => 'Nivel de locación en formulario - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_u')
            ->update([
                'name' => 'configurations_locationLevelForm_u',
                'display_name' => 'Nivel de locación en formulario - editar',
                'description' => 'Nivel de locación en formulario - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','configurations_d')
            ->update([
                'name' => 'configurations_locationLevelForm_d',
                'display_name' => 'Nivel de locación en formulario - eliminar',
                'description' => 'Nivel de locación en formulario - eliminar'
            ]);
    }
}
