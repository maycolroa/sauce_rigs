<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauCtItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_items', function (Blueprint $table) {
            $table->foreign('subobjective_id')->references('id')->on('sau_ct_subobjectives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_items', function (Blueprint $table) {
            $table->dropForeign('sau_ct_items_subobjective_id_foreign');
        });
    }
}
