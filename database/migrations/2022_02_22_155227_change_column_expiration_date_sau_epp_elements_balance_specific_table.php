<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnExpirationDateSauEppElementsBalanceSpecificTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements_balance_specific', function (Blueprint $table) {
            $table->integer('expiration_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_elements_balance_specific', function (Blueprint $table) {
            $table->date('expiration_date')->change();
        });

    }
}
