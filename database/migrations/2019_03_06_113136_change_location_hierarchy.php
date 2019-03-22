<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLocationHierarchy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees_areas', function (Blueprint $table) {
            $table->dropForeign('sau_employees_areas_employee_headquarter_id_foreign');
        });

        Schema::table('sau_employees_areas', function (Blueprint $table) {
            $table->dropColumn('employee_headquarter_id');
        });

        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->dropForeign('sau_employees_processes_employee_area_id_foreign');
        });

        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->dropColumn('employee_area_id');
        });

        Schema::create('sau_headquarter_process', function (Blueprint $table) {
            $table->unsignedInteger('employee_headquarter_id');
            $table->unsignedInteger('employee_process_id');
        });

        Schema::table('sau_headquarter_process', function (Blueprint $table) {
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
        });

        Schema::create('sau_process_area', function (Blueprint $table) {
            $table->unsignedInteger('employee_process_id');
            $table->unsignedInteger('employee_area_id');
        });

        Schema::table('sau_process_area', function (Blueprint $table) {
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_employees_areas', function (Blueprint $table) {
            $table->unsignedInteger('employee_headquarter_id')->after('name')->nullable();
        });

        Schema::table('sau_employees_areas', function (Blueprint $table) {
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters');
        });

        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->unsignedInteger('employee_area_id')->after('name')->nullable();
        });

        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas');
        });

        Schema::table('sau_headquarter_process', function (Blueprint $table) {
            $table->dropForeign('sau_headquarter_process_employee_headquarter_id_foreign');
            $table->dropForeign('sau_headquarter_process_employee_process_id_foreign');
        });
        
        Schema::dropIfExists('sau_headquarter_process');

        Schema::table('sau_process_area', function (Blueprint $table) {
            $table->dropForeign('sau_process_area_employee_process_id_foreign');
            $table->dropForeign('sau_process_area_employee_area_id_foreign');
        });
        
        Schema::dropIfExists('sau_process_area');
    }
}
