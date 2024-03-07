<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPolicyResponsabilitySauRsVehiculesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rs_vehicles', function (Blueprint $table) {
            $table->string('policy_responsability')->nullable()->default('NO')->after('file_mechanical_tech');
            $table->boolean('asigned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rs_vehicles', function (Blueprint $table) {   
            $table->dropColumn('policy_responsability');  
            $table->dropColumn('asigned');  
        });
    }
}
