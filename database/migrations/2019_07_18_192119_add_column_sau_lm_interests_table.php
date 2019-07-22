<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauLmInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_interests', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->nullable()->after('name');

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
        Schema::table('sau_lm_interests', function (Blueprint $table) {
            $table->dropForeign('sau_lm_interests_company_id_foreign');
            $table->dropColumn('company_id');
        });
    }
}
