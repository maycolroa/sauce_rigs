<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsCie10AdicionalesSauReincCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->unsignedInteger('cie10_code_2_id')->after('cie10_code_id')->nullable();
            $table->unsignedInteger('cie10_code_3_id')->after('cie10_code_2_id')->nullable();

            $table->foreign('cie10_code_2_id')->references('id')->on('sau_reinc_cie10_codes')->onDelete('cascade');
            $table->foreign('cie10_code_3_id')->references('id')->on('sau_reinc_cie10_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->dropColumn('cie10_code_2_id');
            $table->dropColumn('cie10_code_3_id');
        });
    }
}
