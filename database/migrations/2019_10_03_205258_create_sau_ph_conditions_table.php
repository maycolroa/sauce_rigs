<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->integer('condition_type_id')->unsigned();
            $table->timestamps();

            $table->foreign('condition_type_id')->references('id')->on('sau_ph_conditions_types');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_conditions');
    }
}
