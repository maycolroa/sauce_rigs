<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauLmArticlesFulfillmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_articles_fulfillment_histories', function (Blueprint $table) {
            $table->string('fulfillment_value')->nullable();
            $table->text('observations')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_articles_fulfillment_histories', function (Blueprint $table) {
            $table->dropColumn('fulfillment_value');
            $table->dropColumn('observations');
        });
    }
}
