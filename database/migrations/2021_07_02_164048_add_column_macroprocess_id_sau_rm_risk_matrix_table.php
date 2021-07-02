<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMacroprocessIdSauRmRiskMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rm_risks_matrix', function (Blueprint $table) {
            $table->integer('macroprocess_id')->after('employee_process_id')->nullable();
        });

        Schema::table('sau_rm_subprocess_risk', function (Blueprint $table) {
            $table->string('nomenclature')->after('risk_sequence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rm_risks_matrix', function (Blueprint $table) {
            $table->dropColumn('macroprocess_id');
        });

        Schema::table('sau_rm_subprocess_risk', function (Blueprint $table) {
            $table->dropColumn('nomenclature');
        });
    }
}
