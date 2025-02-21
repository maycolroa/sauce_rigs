<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationsCriterionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluations_criterion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evaluation_stage_id');
            $table->string('description');

            $table->foreign('evaluation_stage_id')->references('id')->on('sau_bm_evaluations_stages')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_bm_evaluations_criterion');
    }
}
