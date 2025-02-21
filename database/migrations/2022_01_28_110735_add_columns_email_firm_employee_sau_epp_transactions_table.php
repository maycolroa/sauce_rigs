<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsEmailFirmEmployeeSauEppTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_transactions_employees', function (Blueprint $table) {
            $table->string('edit_firm')->nullable();
            $table->string('firm_email')->nullable();
            $table->string('email_firm_employee')->nullable();
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
            $table->dropColumn('edit_firm');
            $table->dropColumn('firm_email');
            $table->dropColumn('email_firm_employee');
        });
    }
}
