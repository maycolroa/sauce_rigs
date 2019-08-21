<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauCtEvaluationContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_contract', function (Blueprint $table) {
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluations')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee');
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluation_contract', function (Blueprint $table) {
            $table->dropForeign('sau_ct_evaluation_contract_evaluation_id_foreign');
            $table->dropForeign('sau_ct_evaluation_contract_contract_id_foreign');
            $table->dropForeign('sau_ct_evaluation_contract_company_id_foreign');
        });
    }
}
