<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtEvaluationContractItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_evaluation_contract_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('evaluation_id');
            $table->unsignedInteger('item_id');
            
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluation_contract')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sau_ct_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_evaluation_contract_items');
    }
}
