<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauActionPlansActivityModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_action_plans_activity_module', function (Blueprint $table) {
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('activity_id');
            $table->integer('item_id');
            $table->string('item_table_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_action_plans_activity_module');
    }
}
