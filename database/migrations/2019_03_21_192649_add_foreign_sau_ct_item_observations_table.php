<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauCtItemObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_item_observations', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('sau_ct_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_item_observations', function (Blueprint $table) {
            $table->dropForeign('sau_ct_item_observations_item_id_foreign');
        });
    }
}
