<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationsStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluations_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evaluation_id');
            $table->string('description');

            $table->foreign('evaluation_id')->references('id')->on('sau_bm_evaluations')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_bm_evaluations_stages');
    }
}
