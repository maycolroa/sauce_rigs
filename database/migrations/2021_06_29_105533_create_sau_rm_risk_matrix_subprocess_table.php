<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmRiskMatrixSubprocessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_risk_matrix_subprocess', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('risk_matrix_id');
            $table->unsignedInteger('sub_process_id');
            $table->foreign('risk_matrix_id')->references('id')->on('sau_rm_risks_matrix')->onDelete('cascade');
            $table->foreign('sub_process_id')->references('id')->on('sau_rm_sub_processes')->onDelete('cascade');

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
        Schema::dropIfExists('sau_rm_risk_matrix_subprocess');
    }
}
