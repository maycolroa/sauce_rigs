<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauEmployeesHeadquartersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees_headquarters', function (Blueprint $table) {
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
        Schema::table('sau_employees_headquarters', function (Blueprint $table) {
            $table->dropForeign('sau_employees_headquarters_employee_regional_id_foreign');
        });
    }
}
