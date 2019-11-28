<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincDiseaseOriginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_disease_origin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_reinc_disease_origin');
    }
}
