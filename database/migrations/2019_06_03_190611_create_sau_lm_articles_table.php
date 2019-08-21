<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->unsignedInteger('law_id');
            $table->string('repelead');
            $table->integer('sequence')->nullable();
            $table->timestamps();

            $table->foreign('law_id')->references('id')->on('sau_lm_laws')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_lm_articles');
    }
}
