<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauInspectConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_inspect_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description',100);
            $table->integer('condition_type_id')->unsigned();

            $table->foreign('condition_type_id')->references('id')->on('sau_inspect_conditions_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_inspect_conditions');
    }
}
