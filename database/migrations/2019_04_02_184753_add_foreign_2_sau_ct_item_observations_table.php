<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeign2SauCtItemObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_item_observations', function (Blueprint $table) {
            $table->unsignedInteger('evaluation_id')->after('id')->nullable(); 
        });

        Schema::table('sau_ct_item_observations', function (Blueprint $table) {
            $table->foreign('evaluation_id')->references('id')->on('sau_ct_item_observations')->onDelete('cascade');
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
            $table->dropForeign('sau_ct_item_observations_evaluation_id_foreign');
        });

        Schema::table('sau_ct_item_observations', function (Blueprint $table) {
            $table->dropColumn('evaluation_id');
        });
    }
}
