<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNomenclatureSauRmCausesControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rm_cause_controls', function (Blueprint $table) {
            $table->string('nomenclature')->after('number_control')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rm_cause_controls', function (Blueprint $table) {
            $table->dropColumn('nomenclature');
        });
    }
}
