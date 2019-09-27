<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_process', function (Blueprint $table) {
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('employee_process_id');
            
            $table->foreign('inspection_id')->references('id')->on('sau_ph_inspections')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspection_process');
    }
}
