<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmArticlesFulfillmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_articles_fulfillment_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fulfillment_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('fulfillment_id')->references('id')->on('sau_lm_articles_fulfillment')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_lm_articles_fulfillment_histories');
    }
}
