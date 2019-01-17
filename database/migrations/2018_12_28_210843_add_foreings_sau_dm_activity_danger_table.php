<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingsSauDmActivityDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->foreign('dm_activity_id')->references('id')->on('sau_danger_matrix_activity');
            $table->foreign('danger_id')->references('id')->on('sau_dm_dangers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->dropForeign('sau_dm_activity_danger_dm_activity_id_foreign');
            $table->dropForeign('sau_dm_activity_danger_danger_id_foreign');
        });
    }
}
