<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnSauActionPlansActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_action_plans_activities', function (Blueprint $table) {
            $table->renameColumn('employee_id','responsible_id');
            $table->dropForeign('sau_action_plans_activities_employee_id_foreign');
            $table->foreign('responsible_id')->references('id')->on('sau_users');
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
            $table->renameColumn('responsible_id','employee_id');
            $table->dropForeign('sau_action_plans_activities_responsible_id_foreign');
            $table->foreign('employee_id')->references('id')->on('sau_employees');
        });
    }
}
