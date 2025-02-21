<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsEditSauCtEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluations', function ($table) {
            $table->boolean('in_edit')->after('creator_user_id')->default(false);
            $table->string('user_edit')->after('in_edit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluations', function (Blueprint $table) {
            $table->dropColumn('in_edit');
            $table->dropColumn('user_edit');
        });
    }
}
