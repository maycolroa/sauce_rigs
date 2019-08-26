<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauDmQualificationDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_danger', function (Blueprint $table) {
            $table->unsignedInteger('type_id')->change();
            $table->foreign('type_id')->references('id')->on('sau_dm_qualification_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_qualification_danger', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_danger_type_id_foreign');
        });
    }
}
