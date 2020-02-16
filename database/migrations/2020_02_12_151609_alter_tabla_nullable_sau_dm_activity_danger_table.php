<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablaNullableSauDmActivityDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->string('danger_generated')->nullable()->change();
            $table->text('possible_consequences_danger')->nullable()->change();
            $table->text('generating_source')->nullable()->change();
            $table->integer('collaborators_quantity')->nullable()->change();
            $table->integer('esd_quantity')->nullable()->change();
            $table->integer('visitor_quantity')->nullable()->change();
            $table->integer('student_quantity')->nullable()->change();
            $table->integer('esc_quantity')->nullable()->change();
            $table->text('existing_controls_engineering_controls')->nullable()->change();
            $table->text('existing_controls_substitution')->nullable()->change();
            $table->text('existing_controls_warning_signage')->nullable()->change();
            $table->text('existing_controls_administrative_controls')->nullable()->change();
            $table->text('existing_controls_epp')->nullable()->change();
            $table->string('legal_requirements')->nullable()->change();
            $table->string('quality_policies')->nullable()->change();
            $table->string('objectives_goals')->nullable()->change();
            $table->string('risk_acceptability')->nullable()->change();
            $table->text('intervention_measures_elimination')->nullable()->change();
            $table->text('intervention_measures_substitution')->nullable()->change();
            $table->text('intervention_measures_engineering_controls')->nullable()->change();
            $table->text('intervention_measures_warning_signage')->nullable()->change();
            $table->text('intervention_measures_administrative_controls')->nullable()->change();
            $table->text('intervention_measures_epp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
