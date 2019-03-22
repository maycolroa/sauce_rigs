<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauDmQualificationMethodologiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_methodologies', function (Blueprint $table) {
            $table->foreign('activity_danger_id')->references('id')->on('sau_dm_activity_danger');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_qualification_methodologies', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_methodologies_activity_danger_id_foreign');
        });
    }
}
