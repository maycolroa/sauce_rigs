<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCtInformationContractLessee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ct_information_contract_lessee', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->integer('nit');
            $table->string('type', 50);
            $table->string('business_name', 100);
            $table->integer('phone');
            $table->string('address', 100);
            $table->string('legal_representative_name', 100);
            $table->string('SG_SST_name', 100);
            $table->integer('number_workers');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ct_information_contract_lessee');
    }
}
