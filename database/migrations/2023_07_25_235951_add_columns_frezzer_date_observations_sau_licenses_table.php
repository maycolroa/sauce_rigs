<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsFrezzerDateObservationsSauLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $table->date('start_freeze')->after('reassigned')->nullable();
            $table->string('observations')->after('start_freeze')->nullable();
            $table->date('date_freeze')->after('observations')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $table->dropColumn('observations');
            $table->dropColumn('date_freeze');
            $table->dropColumn('observations');
        });
    }
}
