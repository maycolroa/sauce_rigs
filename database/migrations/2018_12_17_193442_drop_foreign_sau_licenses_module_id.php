<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignSauLicensesModuleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $table->dropForeign('sau_licenses_module_id_foreign');
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
            $table->foreign('module_id')->references('id')->on('sau_modules');
        });
    }
}
