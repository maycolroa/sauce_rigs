<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnUseCie10SauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $checks = DB::table('sau_reinc_checks')
            ->where('use_cie_10', 'SI')
            ->get();

        foreach ($checks as $check) 
        {
            $check->use_cie_10 = 'Cie 10';
            $check->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
