<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionGeneralElementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transaction_general_element', function (Blueprint $table) {
            $table->unsignedInteger('transaction_general_id');
            $table->unsignedInteger('element_id');
            
            $table->foreign('transaction_general_id','transaction_general_id_foreign')->references('id')->on('sau_epp_transactions_general')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transaction_general_element');
    }
}
