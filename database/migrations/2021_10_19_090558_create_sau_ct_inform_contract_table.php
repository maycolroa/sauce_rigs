<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtInformContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_inform_contract', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('inform_date');
            $table->unsignedInteger('inform_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('contract_id');
            $table->unsignedInteger('evaluator_id');
            $table->string('year');
            $table->string('month');
            $table->string('state')->default('En proceso');
            $table->text('observation')->nullable();
            $table->text('Objective_inform')->nullable();

            $table->foreign('inform_id')->references('id')->on('sau_ct_informs')->onUpdate('cascade')->onDelete('cascade');$table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('evaluator_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_inform_contract');
    }
}
