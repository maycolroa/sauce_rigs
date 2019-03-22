<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtActionItemsLessee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_action_items_lessee', function (Blueprint $table) {
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('action_plan_id');

            $table->foreign('item_id')->references('id')->on('sau_ct_list_check_items')->onDelete('cascade');
            $table->foreign('action_plan_id')->references('id')->on('sau_ct_action_plan_default')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_action_items_lessee');
    }
}
