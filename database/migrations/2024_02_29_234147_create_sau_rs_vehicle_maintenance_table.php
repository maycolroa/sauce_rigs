<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsVehicleMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_vehicle_maintenance', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_id');
            $table->date('date');
            $table->string('type');
            $table->integer('km');
            $table->text('description');
            $table->string('responsible');
            $table->string('apto');
            $table->text('reason')->nullable();
            $table->date('next_date');
            $table->timestamps();


            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_vehicle_maintenance_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_maintenance_id');
            $table->string('file');
            $table->timestamps();


            $table->foreign('vehicle_maintenance_id')->references('id')->on('sau_rs_vehicle_maintenance')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_vehicle_combustible', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_id');
            $table->date('date');
            $table->string('cylinder_capacity');
            $table->integer('km');
            $table->integer('quantity_gallons');
            $table->integer('price_gallons');
            $table->timestamps();


            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_vehicle_maintenance_files');
        Schema::dropIfExists('sau_rs_vehicle_maintenance');
        Schema::dropIfExists('sau_rs_vehicle_combustible');
    }
}
