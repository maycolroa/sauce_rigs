<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionReturnsChangeElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transaction_returns_change_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_employee_id');
            $table->unsignedInteger('element_id');
            $table->unsignedInteger('element_specific_old_id');
            $table->unsignedInteger('element_specific_new_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->string('reason');

            $table->timestamps();

            $table->foreign('element_specific_old_id','element_specific_old_id_foreign')->references('id')->on('sau_epp_elements_balance_specific','exit_detail_id_foreign')->onDelete('cascade');
            $table->foreign('element_specific_new_id','element_specific_new_id_foreign')->references('id')->on('sau_epp_elements_balance_specific')->onDelete('cascade');
            $table->foreign('transaction_employee_id', 'transaction_employee_change_id_foreign')->references('id')->on('sau_epp_transactions_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transaction_returns_change_elements');
    }
}
