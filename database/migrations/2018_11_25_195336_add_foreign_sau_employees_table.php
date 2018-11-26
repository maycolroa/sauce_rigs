<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees', function (Blueprint $table) {
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_employees', function (Blueprint $table) {
            $table->dropForeign('sau_employees_employee_headquarter_id_foreign');
            $table->dropForeign('sau_employees_employee_process_id_foreign');
        });
    }
}
