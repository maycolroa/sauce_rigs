<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->text('observation')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->string('rate')->nullable();
            $table->integer('condition_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('employee_regional_id')->unsigned();
            $table->integer('employee_headquarter_id')->unsigned();
            $table->integer('employee_process_id')->unsigned();
            $table->integer('employee_area_id')->unsigned();  
            $table->text('other_condition')->nullable();
            $table->timestamps();

            $table->foreign('condition_id')->references('id')->on('sau_ph_conditions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('employee_regional_id')->references('id')->on('sau_employees_regionals')->onDelete('cascade');
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_reports');
    }
}
