<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDangerMatrixActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_danger_matrix_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('danger_matrix_id');
            $table->unsignedInteger('activity_id');
            $table->string('type_activity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_danger_matrix_activity');
    }
}
