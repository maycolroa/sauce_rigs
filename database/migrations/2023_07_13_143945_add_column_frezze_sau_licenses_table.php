<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFrezzeSauLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $table->string('freeze')->nullable()->default('NO');
            $table->integer('available_days')->nullable();
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
            $table->dropColumn('freeze');
            $table->dropColumn('available_days');
        });
    }
}
