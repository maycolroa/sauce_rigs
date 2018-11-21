<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsBmAudiometriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
            $table->string('base_type')->nullable();
            $table->integer('base')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
            $table->dropColumn('base_type');
            $table->dropColumn('base');
        });
    }
}
