<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingsSauDangersMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dangers_matrix', function (Blueprint $table) {
            $table->dropForeign('sau_dangers_matrix_user_id_foreign');
            $table->dropForeign('sau_dangers_matrix_company_id_foreign');
        });
    }
}
