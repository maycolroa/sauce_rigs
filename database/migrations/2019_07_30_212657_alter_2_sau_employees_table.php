<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Alter2SauEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees', function (Blueprint $table) {
            $table->unsignedInteger('employee_afp_id')->nullable();
            $table->unsignedInteger('employee_arl_id')->nullable();
            $table->string('contract_numbers')->nullable();
            $table->date('last_contract_date')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('email')->nullable()->change();
            $table->unsignedInteger('employee_headquarter_id')->nullable()->change();
            $table->unsignedInteger('employee_process_id')->nullable()->change();

            $table->foreign('employee_afp_id')->references('id')->on('sau_employees_afp')->onDelete('cascade');
            $table->foreign('employee_arl_id')->references('id')->on('sau_employees_arl')->onDelete('cascade');
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
            $table->dropForeign('sau_employees_employee_afp_id_foreign');
            $table->dropForeign('sau_employees_employee_arl_id_foreign');
            $table->dropColumn('employee_afp_id');
            $table->dropColumn('employee_arl_id');
            $table->dropColumn('contract_numbers');
            $table->dropColumn('last_contract_date');
            $table->dropColumn('contract_type');
        });
    }
}
