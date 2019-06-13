<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmArticlesFulfillmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('fulfillment_value_id');
            $table->text('observations');
            $table->string('file');
            $table->text('responsible');
            $table->timestamps();

            $table->foreign('article_id')->references('id')->on('sau_lm_articles')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('fulfillment_value_id')->references('id')->on('sau_lm_fulfillment_values')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_lm_articles_fulfillment');
    }
}
