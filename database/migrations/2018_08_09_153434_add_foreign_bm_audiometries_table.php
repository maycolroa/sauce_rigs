<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignBmAudiometriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bm_audiometries', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('sau_employees');
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
            $table->dropForeign('bm_audiometries_employee_id_foreign');
        });
    }
}
