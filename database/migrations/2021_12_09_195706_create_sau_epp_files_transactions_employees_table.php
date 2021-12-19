<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppFilesTransactionsEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_files_transactions_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_employee_id');
            $table->string('file');

            $table->timestamps();

            $table->foreign('transaction_employee_id', 'transaction_employee_files_id_foreign')->references('id')->on('sau_epp_transactions_employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_files_transactions_employees');
    }
}
