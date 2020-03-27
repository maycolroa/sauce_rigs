<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtFileDocumentEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_file_document_employee', function (Blueprint $table) {
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('document_id');
            $table->unsignedInteger('file_id');

            $table->foreign('employee_id')->references('id')->on('sau_ct_contract_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('sau_ct_activities_documents')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('sau_ct_file_upload_contracts_leesse')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_file_document_employee');
    }
}
