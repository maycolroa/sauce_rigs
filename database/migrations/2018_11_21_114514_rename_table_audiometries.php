<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableAudiometries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::rename('bm_audiometries', 'sau_bm_audiometries');
      Schema::table('sau_bm_audiometries', function (Blueprint $table) {
        $table->renameIndex('bm_audiometries_employee_id_foreign','sau_bm_audiometries_employee_id_foreign');
        $table->dropForeign('bm_audiometries_employee_id_foreign');
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
      Schema::rename('sau_bm_audiometries', 'bm_audiometries');
      Schema::table('bm_audiometries', function (Blueprint $table) {
        $table->renameIndex('sau_bm_audiometries_employee_id_foreign','bm_audiometries_employee_id_foreign');
        $table->dropForeign('sau_bm_audiometries_employee_id_foreign');
        $table->foreign('employee_id')->references('id')->on('sau_employees');
      });
    }
}
