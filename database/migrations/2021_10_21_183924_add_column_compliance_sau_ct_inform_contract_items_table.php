<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnComplianceSauCtInformContractItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_inform_contract_items', function (Blueprint $table) {
            $table->string('compliance')->after('value_executed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_inform_contract_items', function (Blueprint $table) {
            $table->dropColumn('compliance');
        });
    }
}
