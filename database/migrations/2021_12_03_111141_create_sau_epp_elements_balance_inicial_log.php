<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppElementsBalanceInicialLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_elements_balance_inicial_log', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('element_id');
            $table->boolean('balance_inicial')->default(false);

            $table->timestamps();

            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_elements_balance_inicial_log');
    }
}
