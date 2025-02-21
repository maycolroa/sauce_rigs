<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEmployeePositionEppElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_employee_position_epp_elements', function (Blueprint $table) {
            $table->unsignedInteger('employee_position_id');
            $table->unsignedInteger('element_id');
            
            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_employee_position_epp_elements');
    }
}
