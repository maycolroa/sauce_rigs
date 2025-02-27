<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnYearVehicleSauReVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rs_vehicles', function (Blueprint $table) {   
            $table->integer('year_vehicle')->nullable()->after('name_propietary');
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
            $table->dropColumn('year_vehicle');
        });
    }
}
