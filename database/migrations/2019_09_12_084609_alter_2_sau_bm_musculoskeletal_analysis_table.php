<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter2SauBmMusculoskeletalAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_bm_musculoskeletal_analysis', function (Blueprint $table) {
            $table->string('diagnostic_code_1')->nullable()->change();
            $table->string('diagnostic_code_2')->nullable()->change();
            $table->string('diagnostic_code_3')->nullable()->change();
            $table->string('diagnostic_code_4')->nullable()->change();
            $table->string('diagnostic_code_5')->nullable()->change();
            $table->string('diagnostic_code_6')->nullable()->change();
            $table->string('diagnostic_code_7')->nullable()->change();
            $table->string('diagnostic_code_8')->nullable()->change();
            $table->string('diagnostic_code_9')->nullable()->change();
            $table->string('diagnostic_code_10')->nullable()->change();
            $table->string('diagnostic_code_11')->nullable()->change();
            $table->string('diagnostic_code_12')->nullable()->change();
            $table->string('diagnostic_code_13')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_bm_musculoskeletal_analysis', function (Blueprint $table) {
            $table->string('diagnostic_code_1')->nullable()->change();
            $table->string('diagnostic_code_2')->nullable()->change();
            $table->string('diagnostic_code_3')->nullable()->change();
            $table->string('diagnostic_code_4')->nullable()->change();
            $table->string('diagnostic_code_5')->nullable()->change();
            $table->string('diagnostic_code_6')->nullable()->change();
            $table->string('diagnostic_code_7')->nullable()->change();
            $table->string('diagnostic_code_8')->nullable()->change();
            $table->string('diagnostic_code_9')->nullable()->change();
            $table->string('diagnostic_code_10')->nullable()->change();
            $table->string('diagnostic_code_11')->nullable()->change();
            $table->string('diagnostic_code_12')->nullable()->change();
            $table->string('diagnostic_code_13')->nullable()->change();
        });
    }
}
