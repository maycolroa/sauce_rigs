<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnObservationsSauCtEvaluationContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_contract', function (Blueprint $table) {
            $table->text('observation')->nullable()->after('state');
        });
            
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->dropColumn('observation');
        });
    }
}
