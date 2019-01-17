<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeSauDangerMatrixActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_danger_matrix_activity', function (Blueprint $table) {
            $table->dropForeign('sau_danger_matrix_activity_danger_matrix_id_foreign');
            $table->foreign('danger_matrix_id')
                ->references('id')
                ->on('sau_dangers_matrix')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_danger_matrix_activity', function (Blueprint $table) {
            $table->dropForeign('sau_danger_matrix_activity_danger_matrix_id_foreign');
            $table->foreign('danger_matrix_id')
                ->references('id')
                ->on('sau_dangers_matrix');
        });
    }
}
