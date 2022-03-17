<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkplaceSauLmArticlesFullfilmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->string('workplace')->nullable()->after('responsible');
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
            $table->dropColumn('workplace');
        });
    }
}
