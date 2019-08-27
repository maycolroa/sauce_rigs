<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmActivityDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_activity_danger', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dm_activity_id');
            $table->unsignedInteger('danger_id');
            $table->string('danger_generated');
            $table->string('possible_consequences_danger');
            $table->string('generating_source');
            $table->integer('collaborators_quantity');
            $table->integer('esd_quantity');
            $table->integer('visitor_quantity');
            $table->integer('student_quantity');
            $table->integer('esc_quantity');
            $table->string('existing_controls_engineering_controls');
            $table->string('existing_controls_substitution');
            $table->string('existing_controls_warning_signage');
            $table->string('existing_controls_administrative_controls');
            $table->string('existing_controls_epp');
            $table->string('legal_requirements');
            $table->string('quality_policies');
            $table->string('objectives_goals');
            $table->string('risk_acceptability');
            $table->string('intervention_measures_elimination');
            $table->string('intervention_measures_substitution');
            $table->string('intervention_measures_engineering_controls');
            $table->string('intervention_measures_warning_signage');
            $table->string('intervention_measures_administrative_controls');
            $table->string('intervention_measures_epp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_activity_danger');
    }
}
