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
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas');
            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions');
            $table->foreign('employee_eps_id')->references('id')->on('sau_employees_eps');
            $table->foreign('employee_regional_id')->references('id')->on('sau_employees_regionals');
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
            $table->dropForeign('sau_employees_employee_area_id_foreign');
            $table->dropForeign('sau_employees_employee_position_id_foreign');
            $table->dropForeign('sau_employees_employee_eps_id_foreign');
            $table->dropForeign('sau_employees_employee_regional_id_foreign');
        });
    }
}
