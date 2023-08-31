<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCompanyWorkCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_company_work_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('activity_economic')->nullable();
            $table->string('direction')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->integer('departament_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('zona')->nullable();
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
        Schema::dropIfExists('sau_company_work_centers');
    }
}
