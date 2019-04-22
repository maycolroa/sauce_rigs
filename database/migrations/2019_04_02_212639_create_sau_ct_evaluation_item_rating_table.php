<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtEvaluationItemRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_evaluation_item_rating', function (Blueprint $table) {
            $table->unsignedInteger('evaluation_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('type_rating_id');
            $table->string('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_evaluation_item_rating');
    }
}
