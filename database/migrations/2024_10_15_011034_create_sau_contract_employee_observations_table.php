<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauContractEmployeeObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_contract_employee_observations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();


            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('sau_ct_contract_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contract_employee_observations');
    }
}
