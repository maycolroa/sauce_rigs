<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauEmployeesProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_employees_processes', function (Blueprint $table) {
            $table->dropForeign('sau_employees_processes_employee_area_id_foreign');
        });
    }
}
