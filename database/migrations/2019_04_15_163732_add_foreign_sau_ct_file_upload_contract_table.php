<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauCtFileUploadContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_file_upload_contract', function (Blueprint $table) {
            $table->foreign('file_upload_id')->references('id')->on('sau_ct_file_upload_contracts_leesse')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_file_upload_contract', function (Blueprint $table) {
            $table->dropForeign('sau_ct_file_upload_contract_file_upload_id_foreign');
            $table->dropForeign('sau_ct_file_upload_contract_contract_id_foreign');
        });
    }
}
