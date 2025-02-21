<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppElementsBalanceUbication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_elements_balance_ubication', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('element_id');
            $table->unsignedInteger('location_id');
            $table->integer('quantity');
            $table->integer('quantity_available');
            $table->integer('quantity_allocated');

            $table->timestamps();

            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_epp_elements_balance_ubication');
    }
}
