<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtContractEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_contract_employees', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('name');
            $table->string('identification');
            $table->string('position');
            $table->string('email');

            $table->unsignedInteger('contract_id');

            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contract_employees');
    }
}
