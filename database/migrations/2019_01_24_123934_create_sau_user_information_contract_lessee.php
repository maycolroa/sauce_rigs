<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauUserInformationContractLessee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_user_information_contract_lessee', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('information_id');
            
            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('information_id')->references('id')->on('ct_information_contract_lessee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_user_information_contract_lessee');
    }
}
