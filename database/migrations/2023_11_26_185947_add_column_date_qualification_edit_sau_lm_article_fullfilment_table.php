<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDateQualificationEditSauLmArticleFullfilmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_articles_fulfillment', function (Blueprint $table) {
            $table->datetime('date_qualification_edit')->nullable();
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
            $table->dropColumn('date_qualification_edit');         
        });
    }
}
