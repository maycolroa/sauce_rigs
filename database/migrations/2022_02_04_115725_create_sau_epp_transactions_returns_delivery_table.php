<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionsReturnsDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transactions_returns_delivery', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_employee_id');
            $table->unsignedInteger('delivery_id');
            $table->timestamps();


            $table->foreign('transaction_employee_id', 'transaction_employee_returns_delivery_id_foreign')->references('id')->on('sau_epp_transactions_employees')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('delivery_id')->references('id')->on('sau_epp_transactions_employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transactions_returns_delivery');
    }
}
