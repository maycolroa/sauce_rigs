<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnsAudiometry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
          $table->dropColumn('test_score');
          $table->dropColumn('left_clasification');
          $table->dropColumn('right_clasification');
          $table->dropColumn('work_zone_noise');
          $table->dropColumn('type');
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
          $table->integer('test_score')->nullable();
          $table->string('left_clasification')->nullable();
          $table->string('right_clasification')->nullable();
          $table->string('work_zone_noise')->nullable();
          $table->string('type')->nullable();
        });
    }
}