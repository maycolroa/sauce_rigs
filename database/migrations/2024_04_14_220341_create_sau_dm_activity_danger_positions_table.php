<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmActivityDangerPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_activity_danger_positions', function (Blueprint $table) {
            $table->unsignedInteger('activity_danger_id');
            $table->unsignedInteger('employee_position_id');
            
            $table->foreign('activity_danger_id')->references('id')->on('sau_dm_activity_danger')->onDelete('cascade');
            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_activity_danger_positions');
    }
}
