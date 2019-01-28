<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauActionPlansActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_action_plans_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('user_id');
            $table->date('execution_date');
            $table->date('expiration_date');
            $table->string('state');
            $table->string('editable')->default('SI');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_action_plans_activities');
    }
}
