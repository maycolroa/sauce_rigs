<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionsGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transactions_general', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->integer('quality_elements');
            $table->unsignedInteger('location_id');
            
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transactions_general');
    }
}
