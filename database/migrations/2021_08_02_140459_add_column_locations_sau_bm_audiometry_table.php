<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLocationsSauBmAudiometryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_bm_audiometries', function (Blueprint $table) {
            $table->integer('employee_regional_id')->after('date')->nullable();
            $table->integer('employee_headquarter_id')->after('employee_regional_id')->nullable();
            $table->integer('employee_process_id')->after('employee_headquarter_id')->nullable();
            $table->integer('employee_area_id')->after('employee_process_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_bm_audiometries', function (Blueprint $table) {
            $table->dropColumn('employee_regional_id');
            $table->dropColumn('employee_headquarter_id');
            $table->dropColumn('employee_process_id');
            $table->dropColumn('employee_area_id');
        });
    }
}
