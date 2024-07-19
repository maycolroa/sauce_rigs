<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProyectSauCtInformContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_inform_contract', function (Blueprint $table) {
            $table->integer('proyect_id')->nullable()->after('contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_inform_contract', function (Blueprint $table) {
            $table->dropColumn('proyect_id');
        });
    }
}
