<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauLmArticlesFulfillmentQualificationMasiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->boolean('qualification_masive')->default(false);
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
            $table->dropColumn('qualification_masive');
        });
    }
}
