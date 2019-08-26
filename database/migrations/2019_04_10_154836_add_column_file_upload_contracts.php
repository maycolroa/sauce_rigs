<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFileUploadContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {
            $table->unsignedInteger('contract_id')->after('id');
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
        Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {
            $table->dropForeign('sau_ct_file_upload_contracts_leesse_contract_id_foreign');
            $table->dropColumn('contract_id');
        });
    }
}
