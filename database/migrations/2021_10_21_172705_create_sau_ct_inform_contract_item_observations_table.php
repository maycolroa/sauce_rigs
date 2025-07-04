<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtInformContractItemObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_inform_contract_item_observations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('inform_id');
            $table->unsignedInteger('item_id');
            $table->string('description');
            $table->timestamps();

            $table->foreign('inform_id')->references('id')->on('sau_ct_inform_contract')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sau_ct_inform_theme_item')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_inform_contract_item_observations');
    }
}
