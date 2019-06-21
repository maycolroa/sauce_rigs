<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableArticlesFulfillment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->unsignedInteger('fulfillment_value_id')->nullable()->change();
            $table->text('observations')->nullable()->change();
            $table->string('file')->nullable()->change();
            $table->text('responsible')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->unsignedInteger('fulfillment_value_id')->nullable()->change();
            $table->text('observations')->nullable()->change();
            $table->string('file')->nullable()->change();
            $table->text('responsible')->nullable()->change();
        });
    }
}
