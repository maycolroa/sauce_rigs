<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanyIdTagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_tags_administrative_controls', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name');
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name');
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name');
        });

        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name');
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_tags_administrative_controls', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
