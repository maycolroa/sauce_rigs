<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanySauReincUserHeadquarterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_user_headquarter', function (Blueprint $table) {
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_user_headquarter', function (Blueprint $table) {
            $table->dropForeign('sau_reinc_user_headquarter_company_id_foreign');
            $table->dropColumn('company_id');
        });
    }
}
