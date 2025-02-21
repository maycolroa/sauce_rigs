<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsLocationsInspections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspection_items_qualification_area_location', function (Blueprint $table) {
            $table->unsignedInteger('employee_regional_id')->after('qualification_id')->nullable();
            $table->unsignedInteger('employee_process_id')->after('employee_headquarter_id')->nullable();

            $table->foreign('employee_regional_id','regional_id_foreign')->references('id')->on('sau_employees_regionals')->onDelete('cascade');
            $table->foreign('employee_process_id','process_id_foreign')->references('id')->on('sau_employees_processes')->onDelete('cascade');
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
            $table->dropForeign('regional_id_foreign');
            $table->dropForeign('process_id_foreign');

            $table->dropColumn('employee_regional_id');
            $table->dropColumn('employee_process_id');
        });
    }
}
