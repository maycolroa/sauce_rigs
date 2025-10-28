<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmArticlesFulfillmentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_articles_fulfillment_files', function (Blueprint $table) {
            $table->increments('id'); 
            $table->unsignedInteger('fulfillment_id');
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('company_id');
            $table->string('file');
            
            $table->foreign('fulfillment_id')->references('id')->on('sau_lm_articles_fulfillment')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('sau_lm_articles')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
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
        Schema::dropIfExists('sau_lm_articles_fulfillment_files');
    }
}
