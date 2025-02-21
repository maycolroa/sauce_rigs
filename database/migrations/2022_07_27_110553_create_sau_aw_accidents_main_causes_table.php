<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAwAccidentsMainCausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_accidents_main_causes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('accident_id');
            $table->text('description');
            $table->timestamps();

            $table->foreign('accident_id')->references('id')->on('sau_aw_form_accidents')->onDelete('cascade');
        });

        Schema::create('sau_aw_accidents_secondary_causes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('main_cause_id');
            $table->text('description');
            $table->timestamps();

            $table->foreign('main_cause_id')->references('id')->on('sau_aw_accidents_main_causes')->onDelete('cascade');
        });

        Schema::create('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('secondary_cause_id');
            $table->text('description');
            $table->timestamps();

            $table->foreign('secondary_cause_id')->references('id')->on('sau_aw_accidents_secondary_causes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_aw_accidents_main_causes');
        Schema::dropIfExists('sau_aw_accidents_secondary_causes');
        Schema::dropIfExists('sau_aw_accidents_tertiary_causes');
    }
}
