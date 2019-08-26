<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauLogMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_log_mails', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('id')->nullable();

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
        Schema::table('sau_log_mails', function (Blueprint $table) {
            $table->dropForeign('sau_log_mails_company_id_foreign');
            $table->dropColumn('company_id');
        });
    }
}
