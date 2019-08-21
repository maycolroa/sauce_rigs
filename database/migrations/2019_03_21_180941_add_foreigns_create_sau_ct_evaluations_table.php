<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsCreateSauCtEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluations', function (Blueprint $table) {
            $table->foreign('information_contract_lessee_id')->references('id')->on('sau_ct_information_contract_lessee');
            $table->foreign('company_id')->references('id')->on('sau_companies');
            $table->foreign('creator_user_id')->references('id')->on('sau_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluations', function (Blueprint $table) {
            $table->dropForeign('sau_ct_evaluations_information_contract_lessee_id_foreign');
            $table->dropForeign('sau_ct_evaluations_company_id_foreign');
            $table->dropForeign('sau_ct_evaluations_creator_user_id_foreign');
        });
    }
}
