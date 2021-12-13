<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLocationRenameColumnPositionSauEppTransactionsEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_transactions_employees', function (Blueprint $table) {
            $table->unsignedInteger('location_id');
            $table->renameColumn('position_employee', 'position_employee_id');

            $table->foreign('location_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_transactions_employees', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
}
