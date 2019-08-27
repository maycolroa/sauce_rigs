<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauEmployeesAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees_areas', function (Blueprint $table) {
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters');
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
            $table->dropForeign('sau_employees_areas_employee_headquarter_id_foreign');
        });
    }
}
