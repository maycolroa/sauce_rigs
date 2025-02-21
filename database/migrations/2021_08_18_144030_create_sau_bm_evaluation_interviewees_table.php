<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationIntervieweesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluation_interviewees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('evaluation_id');
            $table->string('name');
            $table->string('position');
            $table->timestamps();


            $table->foreign('evaluation_id')->references('id')->on('sau_bm_evaluation_perform')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_bm_evaluation_interviewees');
    }
}
