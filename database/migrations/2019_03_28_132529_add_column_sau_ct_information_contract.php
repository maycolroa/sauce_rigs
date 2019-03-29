<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauCtInformationContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->string('classification', 50)->after('nit')->nullable();
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
            $table->dropColumn('classification');
        });
    }
}
