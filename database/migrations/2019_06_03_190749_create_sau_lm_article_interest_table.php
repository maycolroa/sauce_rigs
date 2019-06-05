<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmArticleInterestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_article_interest', function (Blueprint $table) {
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('interest_id');
            $table->foreign('article_id')->references('id')->on('sau_lm_articles')->onDelete('cascade');
            $table->foreign('interest_id')->references('id')->on('sau_lm_interests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_lm_article_interest');
    }
}
