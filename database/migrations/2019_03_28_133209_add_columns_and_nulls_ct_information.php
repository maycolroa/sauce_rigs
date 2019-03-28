<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAndNullsCtInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->string('environmental_management_name', 100)->after('legal_representative_name')->nullable();
            $table->string('economic_activity_of_company', 100)->after('environmental_management_name')->nullable();
            $table->string('arl', 100)->after('economic_activity_of_company')->nullable();
            $table->string('risk_class', 50)->after('SG_SST_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->dropColumn('environmental_management_name', 100);
            $table->dropColumn('economic_activity_of_company', 100);
            $table->dropColumn('arl', 100);
            $table->dropColumn('risk_class', 50);
        });
    }
}
