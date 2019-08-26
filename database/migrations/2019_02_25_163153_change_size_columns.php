<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_activities', function (Blueprint $table) {
            $table->text('name')->change();
        });

        Schema::table('sau_dm_dangers', function (Blueprint $table) {
            $table->text('name')->change();
        });

        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->text('possible_consequences_danger')->change();
            $table->text('generating_source')->change();
            $table->text('existing_controls_engineering_controls')->change();
            $table->text('existing_controls_substitution')->change();
            $table->text('existing_controls_warning_signage')->change();
            $table->text('existing_controls_administrative_controls')->change();
            $table->text('existing_controls_epp')->change();
            $table->text('intervention_measures_elimination')->change();
            $table->text('intervention_measures_substitution')->change();
            $table->text('intervention_measures_engineering_controls')->change();
            $table->text('intervention_measures_warning_signage')->change();
            $table->text('intervention_measures_administrative_controls')->change();
            $table->text('intervention_measures_epp')->change();
        });

        Schema::table('sau_tags_administrative_controls', function (Blueprint $table) {
            $table->text('name')->change();
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->text('name')->change();
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->text('name')->change();
        });
        
        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->text('name')->change();
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->text('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_activities', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('sau_dm_dangers', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->string('possible_consequences_danger')->change();
            $table->string('generating_source')->change();
            $table->string('existing_controls_engineering_controls')->change();
            $table->string('existing_controls_substitution')->change();
            $table->string('existing_controls_warning_signage')->change();
            $table->string('existing_controls_administrative_controls')->change();
            $table->string('existing_controls_epp')->change();
            $table->string('intervention_measures_elimination')->change();
            $table->string('intervention_measures_substitution')->change();
            $table->string('intervention_measures_engineering_controls')->change();
            $table->string('intervention_measures_warning_signage')->change();
            $table->string('intervention_measures_administrative_controls')->change();
            $table->string('intervention_measures_epp')->change();
        });

        Schema::table('sau_tags_administrative_controls', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->string('name')->change();
        });
        
        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
}
