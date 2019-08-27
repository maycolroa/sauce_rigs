<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterForeingsSauUserInformationContractLesseeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_user_information_contract_lessee', function (Blueprint $table) {
            $table->dropForeign('sau_user_information_contract_lessee_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            
            $table->dropForeign('sau_user_information_contract_lessee_information_id_foreign');
            $table->foreign('information_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_user_information_contract_lessee', function (Blueprint $table) {
            $table->dropForeign('sau_user_information_contract_lessee_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('sau_users');

            $table->dropForeign('sau_user_information_contract_lessee_information_id_foreign');
            $table->foreign('information_id')->references('id')->on('sau_ct_information_contract_lessee');
        });
    }
}
