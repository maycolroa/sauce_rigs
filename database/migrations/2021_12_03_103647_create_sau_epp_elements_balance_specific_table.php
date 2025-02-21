<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppElementsBalanceSpecificTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_elements_balance_specific', function (Blueprint $table) {
            $table->increments('id');                 
            $table->string('hash');       
            $table->unsignedInteger('element_balance_id');
            $table->unsignedInteger('location_id');
            $table->string('code');
            $table->string('state')->default('Disponible');
            $table->date('expiration_date')->nullable();

            $table->timestamps();

            $table->foreign('element_balance_id')->references('id')->on('sau_epp_elements_balance_ubication')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_epp_elements_balance_specific');
    }
}
