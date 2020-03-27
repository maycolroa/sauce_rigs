<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtContractEmployeeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_contract_employee_activities', function (Blueprint $table) {
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('activity_contract_id');

            $table->foreign('employee_id')->references('id')->on('sau_ct_contract_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('activity_contract_id')->references('id')->on('sau_ct_activities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contract_employee_activities');
    }
}
