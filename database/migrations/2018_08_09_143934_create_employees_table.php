<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('sex');
            $table->string('identification');
            $table->string('email')->unique();
            $table->unsignedInteger('employee_area_id')->nullable();
            $table->unsignedInteger('employee_position_id')->nullable();
            $table->unsignedInteger('employee_regional_id')->nullable();
            $table->unsignedInteger('employee_eps_id')->nullable();
            $table->date('income_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_employees');
    }
}
