<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAudiometries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
          $table->string('gap_left')->nullable();
          $table->string('gap_right')->nullable();
          $table->double('air_left_pta')->nullable();
          $table->double('air_right_pta')->nullable();
          $table->double('osseous_left_pta')->nullable();
          $table->double('osseous_right_pta')->nullable();
          $table->string('severity_grade_air_left_pta')->nullable();
          $table->string('severity_grade_air_right_pta')->nullable();
          $table->string('severity_grade_osseous_left_pta')->nullable();
          $table->string('severity_grade_osseous_right_pta')->nullable();
          $table->string('severity_grade_air_left_4000')->nullable();
          $table->string('severity_grade_air_right_4000')->nullable();
          $table->string('severity_grade_osseous_left_4000')->nullable();
          $table->string('severity_grade_osseous_right_4000')->nullable();
          $table->string('severity_grade_air_left_6000')->nullable();
          $table->string('severity_grade_air_right_6000')->nullable();
          $table->string('severity_grade_air_left_8000')->nullable();
          $table->string('severity_grade_air_right_8000')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
          $table->dropColumn('gap_left');
          $table->dropColumn('gap_right');
          $table->dropColumn('air_left_pta');
          $table->dropColumn('air_right_pta');
          $table->dropColumn('osseous_left_pta');
          $table->dropColumn('osseous_right_pta');
          $table->dropColumn('severity_grade_air_left_pta');
          $table->dropColumn('severity_grade_air_right_pta');
          $table->dropColumn('severity_grade_osseous_left_pta');
          $table->dropColumn('severity_grade_osseous_right_pta');
          $table->dropColumn('severity_grade_air_left_4000');
          $table->dropColumn('severity_grade_air_right_4000');
          $table->dropColumn('severity_grade_osseous_left_4000');
          $table->dropColumn('severity_grade_osseous_right_4000');
          $table->dropColumn('severity_grade_air_left_6000');
          $table->dropColumn('severity_grade_air_right_6000');
          $table->dropColumn('severity_grade_air_left_8000');
          $table->dropColumn('severity_grade_air_right_8000');
        });
    }
}
