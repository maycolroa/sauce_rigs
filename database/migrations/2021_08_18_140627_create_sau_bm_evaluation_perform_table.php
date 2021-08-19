<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationPerformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluation_perform', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('evaluation_date');
            $table->unsignedInteger('evaluation_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('evaluator_id');
            $table->string('type');
            $table->string('state')->default('En proceso');
            $table->text('observation')->nullable();

            $table->foreign('evaluation_id')->references('id')->on('sau_bm_evaluations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('evaluator_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_bm_evaluation_perform');
    }
}
