<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDxsMultiplesSauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {

            //////cie10////
            $table->unsignedInteger('cie10_code_4_id')->after('cie10_code_id')->nullable();
            $table->unsignedInteger('cie10_code_5_id')->after('cie10_code_2_id')->nullable();

            $table->foreign('cie10_code_4_id')->references('id')->on('sau_reinc_cie10_codes')->onDelete('cascade');
            $table->foreign('cie10_code_5_id')->references('id')->on('sau_reinc_cie10_codes')->onDelete('cascade');

            //////evento///
            $table->string('disease_origin_2')->nullable();
            $table->string('disease_origin_3')->nullable();
            $table->string('disease_origin_4')->nullable();
            $table->string('disease_origin_5')->nullable();

            /////lateralidad////            
            $table->string('laterality_2')->nullable();
            $table->string('laterality_3')->nullable();
            $table->string('laterality_4')->nullable();
            $table->string('laterality_5')->nullable();

            ///////calificacion DME/////
            $table->string('qualification_dme_2')->nullable();
            $table->string('qualification_dme_3')->nullable();
            $table->string('qualification_dme_4')->nullable();
            $table->string('qualification_dme_5')->nullable();
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
            $table->dropColumn('cie10_code_4_id');
            $table->dropColumn('cie10_code_5_id');
            $table->dropColumn('disease_origin_2');
            $table->dropColumn('disease_origin_3');
            $table->dropColumn('disease_origin_4');
            $table->dropColumn('disease_origin_5');
            $table->dropColumn('laterality_2');
            $table->dropColumn('laterality_3');
            $table->dropColumn('laterality_4');
            $table->dropColumn('laterality_5');
            $table->dropColumn('qualification_dme_2');
            $table->dropColumn('qualification_dme_3');
            $table->dropColumn('qualification_dme_4');
            $table->dropColumn('qualification_dme_5');
        });
    }
}
