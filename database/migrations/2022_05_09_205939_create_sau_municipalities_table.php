<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauMunicipalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_municipalities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('departament_id');
            $table->string('name');
            $table->string('code');

            $table->timestamps();

            $table->foreign('departament_id')->references('id')->on('sau_departaments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_municipalities');
    }
}
