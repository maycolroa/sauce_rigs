<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsDriverVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_driver_vehicles', function (Blueprint $table) {
            $table->unsignedInteger('driver_id');
            $table->unsignedInteger('vehicle_id');
            
            $table->foreign('driver_id')->references('id')->on('sau_rs_drivers')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onDelete('cascade');
        });

        Schema::table('sau_rs_drivers', function (Blueprint $table) {  
            $table->dropForeign('sau_rs_drivers_vehicle_id_foreign');          
            $table->dropColumn('vehicle_id');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_driver_vehicles');

        Schema::table('sau_rs_drivers', function (Blueprint $table) {            
            $table->unsignedInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
