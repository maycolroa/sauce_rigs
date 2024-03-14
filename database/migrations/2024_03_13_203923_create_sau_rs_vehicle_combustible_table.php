<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsVehicleCombustibleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_vehicle_combustibles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('driver_id');
            $table->date('date');
            $table->integer('km');
            $table->text('cylinder_capacity');
            $table->integer('quantity_galons');
            $table->integer('price_galon');
            $table->timestamps();


            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('sau_rs_drivers')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::dropIfExists('sau_rs_vehicle_combustible');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_vehicle_combustibles');
    }
}
