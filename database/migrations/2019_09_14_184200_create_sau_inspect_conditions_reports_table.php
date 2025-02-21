<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauInspectConditionsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_inspect_conditions_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->text('observation');
            $table->string('image_1',60)->nullable();
            $table->string('image_2',60)->nullable();
            $table->string('image_3',60)->nullable();
            $table->string('rate');
            $table->integer('condition_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('headquarter_id')->unsigned();
            $table->integer('regional_id')->unsigned();
            $table->integer('process_id')->unsigned();
            $table->integer('area_id')->unsigned();  
            $table->timestamps();
            $table->string('other_condition')->nullable();

            $table->foreign('condition_id')->references('id')->on('sau_inspect_conditions');
            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('regional_id')->references('id')->on('sau_employees_regionals');
            $table->foreign('headquarter_id')->references('id')->on('sau_employees_headquarters');
            $table->foreign('process_id')->references('id')->on('sau_employees_processes');
            $table->foreign('area_id')->references('id')->on('sau_employees_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_inspect_conditions_reports');
    }
}
