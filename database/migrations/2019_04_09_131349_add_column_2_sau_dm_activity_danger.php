<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumn2SauDmActivityDanger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->string('danger_description')->after('danger_id')->nullable();
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
            $table->dropColumn('danger_description');
        });
    }
}
