<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCtItemDescriptionActionPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_item_description_action_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('list_check_item_id');
            $table->text('description');
            $table->string('state');
            
            $table->foreign('list_check_item_id')->references('id')->on('sau_ct_list_check_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_item_description_action_plan');
    }
}
