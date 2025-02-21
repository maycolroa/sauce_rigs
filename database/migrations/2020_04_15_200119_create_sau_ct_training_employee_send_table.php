<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtTrainingEmployeeSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_training_employee_send', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("training_id");
            $table->unsignedInteger("employee_id");

            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('sau_ct_trainings')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('sau_ct_contract_employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_training_employee_send');
    }
}
