<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLocationIdSauEppElementsBalanceInicialLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements_balance_inicial_log', function (Blueprint $table) {
            $table->unsignedInteger('location_id');

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
        Schema::table('sau_epp_elements_balance_inicial_log', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
}
