<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserPromotorSauLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('company_id')->nullable();

            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_licenses', function (Blueprint $table) {
            $$table->dropForeign('sau_licenses_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
