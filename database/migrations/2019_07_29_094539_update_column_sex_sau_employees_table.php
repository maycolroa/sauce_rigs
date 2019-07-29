<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnSexSauEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('sau_employees')
            ->where('sex','M')
            ->update([
                'sex' => 'Masculino'
            ]);
        
        DB::table('sau_employees')
            ->where('sex','F')
            ->update([
                'sex' => 'Femenino'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sau_employees')
            ->where('sex','Masculino')
            ->update([
                'sex' => 'M'
            ]);
        
        DB::table('sau_employees')
            ->where('sex','Femenino')
            ->update([
                'sex' => 'F'
            ]);
    }
}
