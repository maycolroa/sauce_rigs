<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesTagsRsVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_tag_plate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_name_propietaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_type_vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_mark', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_line', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_capacity_loading', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_color', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_tag_plate');
        Schema::dropIfExists('sau_rs_tag_name_propietaries');
        Schema::dropIfExists('sau_rs_tag_type_vehicles');
        Schema::dropIfExists('sau_rs_tag_mark');
        Schema::dropIfExists('sau_rs_tag_line');
        Schema::dropIfExists('sau_rs_tag_model');
        Schema::dropIfExists('sau_rs_tag_capacity_loading');
        Schema::dropIfExists('sau_rs_tag_color');
    }
}
