<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameModuleSiProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sau_modules')
            ->where('name','activities')
            ->update([
                'display_name' => 'Actividades'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_c')
            ->update([
                'display_name' => 'Actividades - crear',
                'description' => 'Actividades - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_r')
            ->update([
                'display_name' => 'Actividades - ver',
                'description' => 'Actividades - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_u')
            ->update([
                'display_name' => 'Actividades - editar',
                'description' => 'Actividades - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_d')
            ->update([
                'display_name' => 'Actividades - eliminar',
                'description' => 'Actividades - eliminar'
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
            ->where('name','activities')
            ->update([
                'display_name' => 'SI - Procesos'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_c')
            ->update([
                'display_name' => 'SI - Procesos - crear',
                'description' => 'SI - Procesos - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_r')
            ->update([
                'display_name' => 'SI - Procesos - ver',
                'description' => 'SI - Procesos - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_u')
            ->update([
                'display_name' => 'SI - Procesos - editar',
                'description' => 'SI - Procesos - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','activities_d')
            ->update([
                'display_name' => 'SI - Procesos - eliminar',
                'description' => 'SI - Procesos - eliminar'
            ]);
    }
}
