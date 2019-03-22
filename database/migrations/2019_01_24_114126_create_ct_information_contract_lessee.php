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
        Schema::create('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->integer('nit');
            $table->string('type', 50);
            $table->string('classification', 50)->nullable();
            $table->string('business_name', 100);
            $table->integer('phone')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('legal_representative_name', 100)->nullable();
            $table->string('environmental_management_name', 100)->nullable();
            $table->string('economic_activity_of_company', 100)->nullable();
            $table->string('arl', 100)->nullable();
            $table->string('SG_SST_name', 100)->nullable();
            $table->string('risk_class', 50)->nullable();
            $table->integer('number_workers')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_information_contract_lessee');
    }
}
