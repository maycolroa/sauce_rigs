<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauActionPlansActivityModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_action_plans_activity_module', function (Blueprint $table) {
            $table->foreign('module_id')->references('id')->on('sau_modules');
            $table->foreign('activity_id')->references('id')->on('sau_action_plans_activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_action_plans_activity_module', function (Blueprint $table) {
            $table->dropForeign('sau_action_plans_activity_module_module_id_foreign');
            $table->dropForeign('sau_action_plans_activity_module_activity_id_foreign');
        });
    }
}
