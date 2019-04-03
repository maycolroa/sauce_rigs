<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnSauCtEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluations', function (Blueprint $table) {
            $table->dropColumn('information_contract_lessee_id');
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
            $table->unsignedInteger('information_contract_lessee_id');
        });
    }
}
