<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;

class UpdateColumnUseCie10SauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $checks = Check::where('use_cie_10', 'SI')->withoutGlobalScopes()->get();

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
