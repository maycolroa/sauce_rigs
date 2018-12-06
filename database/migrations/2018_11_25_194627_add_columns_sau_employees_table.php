<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees', function (Blueprint $table) {
            $table->unsignedInteger('employee_headquarter_id')->nullable();
            $table->unsignedInteger('employee_process_id')->nullable();
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
            $table->dropColumn('employee_headquarter_id');
            $table->dropColumn('employee_process_id');
        });
    }
}
