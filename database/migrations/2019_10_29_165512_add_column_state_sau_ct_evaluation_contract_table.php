<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStateSauCtEvaluationContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_contract', function (Blueprint $table) {
            $table->string('state')->after('evaluator_id')->default('En proceso');
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
            $table->dropColumn('state');
        });
    }
}
