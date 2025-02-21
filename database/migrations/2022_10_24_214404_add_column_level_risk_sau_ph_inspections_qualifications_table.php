<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLevelRiskSauPhInspectionsQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspection_items_qualification_area_location', function (Blueprint $table) {
            $table->string('level_risk')->after('find')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_inspection_items_qualification_area_location', function (Blueprint $table) {
            $table->dropColumn('level_risk');
        });
    }
}
