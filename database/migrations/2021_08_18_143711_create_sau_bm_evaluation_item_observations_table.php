<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationItemObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluation_item_observations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('evaluation_id');
            $table->unsignedInteger('item_id');
            $table->string('description');
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('sau_bm_evaluation_perform')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sau_bm_evaluations_items')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_bm_evaluation_item_observations');
    }
}
