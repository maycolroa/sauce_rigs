<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluationPerformItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluation_perform_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evaluation_id');
            $table->unsignedInteger('item_id');
            $table->string('value')->nullable();

            $table->foreign('evaluation_id')->references('id')->on('sau_bm_evaluation_perform')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sau_bm_evaluations_items')->onUpdate('cascade')->onDelete('cascade');
            
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
        Schema::dropIfExists('sau_bm_evaluation_perform_items');
    }
}
