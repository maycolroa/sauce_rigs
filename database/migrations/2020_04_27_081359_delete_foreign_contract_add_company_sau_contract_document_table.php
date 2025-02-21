<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteForeignContractAddCompanySauContractDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_contracts_documents', function (Blueprint $table) {

            $table->dropForeign('sau_ct_contracts_documents_contract_id_foreign');

            $table->dropColumn('contract_id');

            $table->unsignedInteger('company_id');

            $table->foreign('company_id')
                ->references('id')
                ->on('sau_companies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
