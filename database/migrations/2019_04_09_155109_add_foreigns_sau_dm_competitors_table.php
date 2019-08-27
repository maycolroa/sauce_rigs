<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauDmCompetitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_competitors', function (Blueprint $table) {
            $table->foreign('danger_matrix_id')->references('id')->on('sau_dangers_matrix')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_competitors', function (Blueprint $table) {
            $table->dropForeign('sau_dm_competitors_danger_matrix_id_foreign');
            $table->dropForeign('sau_dm_competitors_user_id_foreign');
        });
    }
}
