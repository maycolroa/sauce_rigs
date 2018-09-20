<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableBmAudiometriesAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
          $table->renameColumn('left_500','air_left_500');
          $table->renameColumn('left_1000','air_left_1000');
          $table->renameColumn('left_2000','air_left_2000');
          $table->renameColumn('left_3000','air_left_3000');
          $table->renameColumn('left_4000','air_left_4000');
          $table->renameColumn('left_6000','air_left_6000');
          $table->renameColumn('left_8000','air_left_8000');
          $table->renameColumn('right_500','air_right_500');
          $table->renameColumn('right_1000','air_right_1000');
          $table->renameColumn('right_2000','air_right_2000');
          $table->renameColumn('right_3000','air_right_3000');
          $table->renameColumn('right_4000','air_right_4000');
          $table->renameColumn('right_6000','air_right_6000');
          $table->renameColumn('right_8000','air_right_8000');
          $table->integer('osseous_left_500');
          $table->integer('osseous_left_1000');
          $table->integer('osseous_left_2000');
          $table->integer('osseous_left_3000');
          $table->integer('osseous_left_4000');
          $table->integer('osseous_right_500');
          $table->integer('osseous_right_1000');
          $table->integer('osseous_right_2000');
          $table->integer('osseous_right_3000');
          $table->integer('osseous_right_4000');
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
          $table->renameColumn('air_left_500','left_500');
          $table->renameColumn('air_left_1000','left_1000');
          $table->renameColumn('air_left_2000','left_2000');
          $table->renameColumn('air_left_3000','left_3000');
          $table->renameColumn('air_left_4000','left_4000');
          $table->renameColumn('air_left_6000','left_6000');
          $table->renameColumn('air_left_8000','left_8000');
          $table->renameColumn('air_right_500','right_500');
          $table->renameColumn('air_right_1000','right_1000');
          $table->renameColumn('air_right_2000','right_2000');
          $table->renameColumn('air_right_3000','right_3000');
          $table->renameColumn('air_right_4000','right_4000');
          $table->renameColumn('air_right_6000','right_6000');
          $table->renameColumn('air_right_8000','right_8000');
          $table->dropColumn('osseous_left_500');
          $table->dropColumn('osseous_left_1000');
          $table->dropColumn('osseous_left_2000');
          $table->dropColumn('osseous_left_3000');
          $table->dropColumn('osseous_left_4000');
          $table->dropColumn('osseous_right_500');
          $table->dropColumn('osseous_right_1000');
          $table->dropColumn('osseous_right_2000');
          $table->dropColumn('osseous_right_3000');
          $table->dropColumn('osseous_right_4000');
        });
    }
}
