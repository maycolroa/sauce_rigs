<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmObservationQualificationSauDmActivityDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_activity_danger', function (Blueprint $table) {
            $table->text('observation_qualifications')->nullable();
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
            $table->dropColumn('observation_qualifications');
        });
    }
}
