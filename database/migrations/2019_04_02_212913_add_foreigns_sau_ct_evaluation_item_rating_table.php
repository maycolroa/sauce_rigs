<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauCtEvaluationItemRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_item_rating', function (Blueprint $table) {
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluation_contract')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('sau_ct_items')->onDelete('cascade');
            $table->foreign('type_rating_id')->references('id')->on('sau_ct_types_ratings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluation_item_rating', function (Blueprint $table) {
            $table->dropForeign('sau_ct_evaluation_item_rating_evaluation_id_foreign');
            $table->dropForeign('sau_ct_evaluation_item_rating_item_id_foreign');
            $table->dropForeign('sau_ct_evaluation_item_rating_type_rating_id_foreign');
        });
    }
}
