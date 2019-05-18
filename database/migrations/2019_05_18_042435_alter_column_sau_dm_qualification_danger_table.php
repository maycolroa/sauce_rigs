<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnSauDmQualificationDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_danger', function (Blueprint $table) {
            $table->string('value_id')->change();
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
            $table->integer('value_id')->change();
        });
    }
}
