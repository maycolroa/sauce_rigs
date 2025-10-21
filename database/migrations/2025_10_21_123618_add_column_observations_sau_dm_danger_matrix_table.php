<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnObservationsSauDmDangerMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {   
            $table->text('observations')->nullable()->after('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {            
            $table->dropColumn('observations');      
        });
    }
}
