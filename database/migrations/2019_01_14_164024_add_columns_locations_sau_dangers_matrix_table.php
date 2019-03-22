<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsLocationsSauDangersMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {
            $table->unsignedInteger('employee_regional_id')->nullable()->after('company_id');
            $table->unsignedInteger('employee_headquarter_id')->nullable()->after('employee_regional_id');
            $table->unsignedInteger('employee_area_id')->nullable()->after('employee_headquarter_id');
            $table->unsignedInteger('employee_process_id')->nullable()->after('employee_area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {
            $table->dropColumn('employee_regional_id');
            $table->dropColumn('employee_headquarter_id');
            $table->dropColumn('employee_area_id');
            $table->dropColumn('employee_process_id');
        });
    }
}
