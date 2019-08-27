<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInfoContractLessee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->integer('phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('legal_representative_name')->nullable()->change();
            $table->string('SG_SST_name')->nullable()->change();
            $table->integer('number_workers')->nullable()->change();
            $table->integer('high_risk_work')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->integer('phone')->default()->change();
            $table->string('address')->default()->change();
            $table->string('legal_representative_name')->default()->change();
            $table->string('SG_SST_name')->default()->change();
            $table->integer('number_workers')->default()->change();
            $table->integer('high_risk_work')->default()->change();
        });
    }
}
