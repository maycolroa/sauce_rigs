<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignSauCtIntervieweesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_interviewees', function (Blueprint $table) {
            $table->dropForeign('sau_ct_interviewees_evaluation_id_foreign');
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluation_contract')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_interviewees', function (Blueprint $table) {
            $table->dropForeign('sau_ct_interviewees_evaluation_id_foreign');
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluations')->onDelete('cascade');
        });
    }
}
