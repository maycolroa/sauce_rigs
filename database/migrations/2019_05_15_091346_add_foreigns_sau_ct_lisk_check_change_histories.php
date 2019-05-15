<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauCtLiskCheckChangeHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_lisk_check_change_histories', function (Blueprint $table) {
            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_lisk_check_change_histories', function (Blueprint $table) {
            $table->dropForeign('sau_ct_lisk_check_change_histories_contract_id_foreign');
            $table->dropForeign('sau_ct_lisk_check_change_histories_user_id_foreign');
        });
    }
}
