<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignSauCtEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluations', function (Blueprint $table) {
            $table->dropForeign('sau_ct_evaluations_information_contract_lessee_id_foreign');
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
            $table->foreign('information_contract_lessee_id')->references('id')->on('sau_ct_information_contract_lessee');
        });
    }
}
