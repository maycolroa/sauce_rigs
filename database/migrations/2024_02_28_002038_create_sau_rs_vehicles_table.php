<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->text('plate');
            $table->text('name_propietary');
            $table->string('registration_number');
            $table->date('registration_number_date');
            $table->unsignedInteger('employee_regional_id')->nullable();
            $table->unsignedInteger('employee_headquarter_id')->nullable();
            $table->unsignedInteger('employee_area_id')->nullable();
            $table->unsignedInteger('employee_process_id')->nullable();
            $table->text('type_vehicle');
            $table->string('code_vehicle')->nullable();
            $table->text('mark');
            $table->text('line');
            $table->text('model');
            $table->integer('cylinder_capacity');
            $table->string('color');
            $table->string('chassis_number');
            $table->string('engine_number');
            $table->integer('passenger_capacity');
            $table->string('loading_capacity');
            $table->string('state');
            $table->string('soat_number');
            $table->string('insurance');
            $table->string('expedition_date_soat');
            $table->string('due_date_soat');
            $table->string('file_soat')->nullable();
            $table->string('mechanical_tech_number');
            $table->string('issuing_entity');
            $table->string('expedition_date_mechanical_tech');
            $table->string('due_date_mechanical_tech');
            $table->string('file_mechanical_tech');
            $table->string('policy_number')->nullable();
            $table->string('policy_entity')->nullable();
            $table->string('expedition_date_policy')->nullable();
            $table->string('due_date_policy')->nullable();
            $table->string('file_policy')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_regional_id')->references('id')->on('sau_employees_regionals')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_vehicles');
    }
}
