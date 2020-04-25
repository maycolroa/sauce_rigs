<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtFileDocumentContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_file_document_contract', function (Blueprint $table) {
            $table->unsignedInteger('contract_id');
            $table->unsignedInteger('document_id');
            $table->unsignedInteger('file_id');

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('sau_ct_contracts_documents')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_ct_file_document_contract');
    }
}
