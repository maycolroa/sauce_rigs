<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDeadlineMotiveFileSauCtContractEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {
            $table->date('deadline')->nullable();
            $table->string('motive_inactivation')->nullable();
            $table->string('file_inactivation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {
            $table->dropColumn('motive_inactivation');
            $table->dropColumn('deadline');
            $table->dropColumn('file_inactivation');
        });
    }
}
