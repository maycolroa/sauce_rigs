<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPercentageComplianceSauBmEvaluationsPerformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_bm_evaluation_perform', function (Blueprint $table) {
            $table->string('percentage_compliance')->after('state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_bm_evaluation_perform', function (Blueprint $table) {
            $table->dropColumn('percentage_compliance');
        });
    }
}
