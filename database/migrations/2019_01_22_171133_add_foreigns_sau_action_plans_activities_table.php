<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauActionPlansActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_action_plans_activities', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('sau_employees');
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
        Schema::table('sau_action_plans_activities', function (Blueprint $table) {
            $table->dropForeign('sau_action_plans_activities_employee_id_foreign');
            $table->dropForeign('sau_action_plans_activities_user_id_foreign');
        });
    }
}
