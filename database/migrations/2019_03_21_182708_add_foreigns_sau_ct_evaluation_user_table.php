<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauCtEvaluationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_user', function (Blueprint $table) {
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluation_user', function (Blueprint $table) {
            $table->dropForeign('sau_ct_evaluation_user_evaluation_id_foreign');
            $table->dropForeign('sau_ct_evaluation_user_user_id_foreign');
        });
    }
}
