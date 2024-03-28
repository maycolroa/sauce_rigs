<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsDriverInfractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_infractions_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('sau_rs_infractions_type_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_id');
            $table->string('code');
            $table->text('description');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('sau_rs_infractions_type')->onDelete('cascade');
        });

        Schema::create('sau_rs_driver_infractions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id'); 
            $table->unsignedInteger('driver_id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('type_id');
            $table->date('date');
            $table->date('date_simit');
            $table->timestamps();


            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('sau_rs_drivers')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('sau_rs_infractions_type')->onDelete('cascade');
        });

        Schema::create('sau_rs_driver_infractions_codes', function (Blueprint $table) {
            $table->unsignedInteger('infraction_id');
            $table->unsignedInteger('code_id');
            
            $table->foreign('infraction_id')->references('id')->on('sau_rs_driver_infractions')->onDelete('cascade');
            $table->foreign('code_id')->references('id')->on('sau_rs_infractions_type_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_driver_infractions_codes');
        Schema::dropIfExists('sau_rs_driver_infractions');
        Schema::dropIfExists('sau_rs_infractions_type_codes');
        Schema::dropIfExists('sau_rs_infractions_type');
    }
}
