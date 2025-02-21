<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionEmployeeElementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transaction_employee_element', function (Blueprint $table) {
            $table->unsignedInteger('transaction_employee_id');
            $table->unsignedInteger('element_id');
            
            $table->foreign('transaction_employee_id','transaction_employee_id_foreign')->references('id')->on('sau_epp_transactions_employees')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('sau_epp_elements_balance_specific')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transaction_employee_element');
    }
}
