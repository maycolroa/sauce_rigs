<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauCtSubobjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_subobjectives', function (Blueprint $table) {
            $table->foreign('objective_id')->references('id')->on('sau_ct_objectives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_subobjectives', function (Blueprint $table) {
            $table->dropForeign('sau_ct_subobjectives_objective_id_foreign');
        });
    }
}
