<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameModuleMacroprocesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sau_modules')
            ->where('name','processes')
            ->update([
                'display_name' => 'Procesos'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_c')
            ->update([
                'display_name' => 'Procesos - crear',
                'description' => 'Procesos - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_r')
            ->update([
                'display_name' => 'Procesos - ver',
                'description' => 'Procesos - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_u')
            ->update([
                'display_name' => 'Procesos - editar',
                'description' => 'Procesos - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_d')
            ->update([
                'display_name' => 'Procesos - eliminar',
                'description' => 'Procesos - eliminar'
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
            ->where('name','processes')
            ->update([
                'display_name' => 'Macroprocesos'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_c')
            ->update([
                'display_name' => 'Macroprocesos - crear',
                'description' => 'Macroprocesos - crear'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_r')
            ->update([
                'display_name' => 'Macroprocesos - ver',
                'description' => 'Macroprocesos - ver'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_u')
            ->update([
                'display_name' => 'Macroprocesos - editar',
                'description' => 'Macroprocesos - editar'
            ]);

        DB::table('sau_permissions')
            ->where('name','processes_d')
            ->update([
                'display_name' => 'Macroprocesos - eliminar',
                'description' => 'Macroprocesos - eliminar'
            ]);
    }
}
