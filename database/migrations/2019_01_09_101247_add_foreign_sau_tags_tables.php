<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauTagsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_tags_administrative_controls', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });

        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
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
            $table->dropForeign('sau_tags_administrative_controls_company_id_foreign');
        });

        Schema::table('sau_tags_engineering_controls', function (Blueprint $table) {
            $table->dropForeign('sau_tags_engineering_controls_company_id_foreign');
        });

        Schema::table('sau_tags_epp', function (Blueprint $table) {
            $table->dropForeign('sau_tags_epp_company_id_foreign');
        });

        Schema::table('sau_tags_possible_consequences_danger', function (Blueprint $table) {
            $table->dropForeign('sau_tags_possible_consequences_danger_company_id_foreign');
        });

        Schema::table('sau_tags_warning_signage', function (Blueprint $table) {
            $table->dropForeign('sau_tags_warning_signage_company_id_foreign');
        });
    }
}
