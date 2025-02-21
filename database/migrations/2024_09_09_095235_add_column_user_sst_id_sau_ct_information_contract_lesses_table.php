<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserSstIdSauCtInformationContractLessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {
            $table->unsignedInteger('user_sst_id')->nullable();
            $table->foreign('user_sst_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropColumn('user_sst_id');
        });
    }
}
