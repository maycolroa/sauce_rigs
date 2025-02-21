<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterForeingKeysLocalization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_reports', function (Blueprint $table) {
            $table->integer('employee_regional_id')->unsigned()->nullable()->change();
            $table->integer('employee_headquarter_id')->unsigned()->nullable()->change();
            $table->integer('employee_process_id')->unsigned()->nullable()->change();
            $table->integer('employee_area_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_reports', function (Blueprint $table) {
            $table->integer('employee_regional_id')->unsigned()->nullable()->change();
            $table->integer('employee_headquarter_id')->unsigned()->nullable()->change();
            $table->integer('employee_process_id')->unsigned()->nullable()->change();
            $table->integer('employee_area_id')->unsigned()->nullable()->change();
        });
    }
}
