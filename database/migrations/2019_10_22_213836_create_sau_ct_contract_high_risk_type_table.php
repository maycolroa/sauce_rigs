<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtContractHighRiskTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_contract_high_risk_type', function (Blueprint $table) {
            $table->unsignedInteger("contract_id");
            $table->unsignedInteger("high_risk_type_id");

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
            $table->foreign('high_risk_type_id')->references('id')->on('sau_ct_high_risk_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contract_high_risk_type');
    }
}
