<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauCompanyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_company_user', function (Blueprint $table) {
            $table->dropForeign('sau_company_user_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            
            $table->dropForeign('sau_company_user_company_id_foreign');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_company_user', function (Blueprint $table) {
            $table->dropForeign('sau_company_user_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('sau_users');

            $table->dropForeign('sau_company_user_company_id_foreign');
            $table->foreign('company_id')->references('id')->on('sau_companies');

            $table->timestamps();
        });
    }
}
