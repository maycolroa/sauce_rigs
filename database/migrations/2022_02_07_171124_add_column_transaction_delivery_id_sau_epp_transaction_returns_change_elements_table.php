<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTransactionDeliveryIdSauEppTransactionReturnsChangeElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_transaction_returns_change_elements', function (Blueprint $table) {
            $table->unsignedInteger('transaction_delivery_id')->after('transaction_employee_id');

            $table->foreign('transaction_delivery_id', 'transaction_delivery_change_id_foreign')->references('id')->on('sau_epp_transactions_employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_transaction_returns_change_elements', function (Blueprint $table) {
            $table->dropColumn('transaction_delivery_id');
        });
    }
}
