<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtProyectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_proyects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_ct_contracts_proyects', function (Blueprint $table) {
            $table->unsignedInteger("contract_id");
            $table->unsignedInteger("proyect_id");

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
            $table->foreign('proyect_id')->references('id')->on('sau_ct_proyects')->onDelete('cascade');
        });

        Schema::create('sau_ct_contract_employee_proyects', function (Blueprint $table) {
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('proyect_contract_id');

            $table->foreign('employee_id')->references('id')->on('sau_ct_contract_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('proyect_contract_id')->references('id')->on('sau_ct_proyects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contract_employee_proyects');
        Schema::dropIfExists('sau_ct_contracts_proyects');
        Schema::dropIfExists('sau_ct_proyects');
    }
}
