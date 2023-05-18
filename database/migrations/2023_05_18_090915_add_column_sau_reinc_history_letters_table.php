<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauReincHistoryLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_letter_recommendations_history', function (Blueprint $table) {
            $table->boolean('not_recommendations')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_letter_recommendations_history', function (Blueprint $table) {
            $table->dropColumn('not_recommendations');
        });
    }
}
