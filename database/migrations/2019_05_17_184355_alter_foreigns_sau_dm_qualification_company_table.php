<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterForeignsSauDmQualificationCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_company', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_company_company_id_foreign');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            
            $table->dropForeign('sau_dm_qualification_company_qualification_id_foreign');
            $table->foreign('qualification_id')->references('id')->on('sau_dm_qualifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_qualification_company', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_company_company_id_foreign');
            $table->foreign('company_id')->references('id')->on('sau_companies');

            $table->dropForeign('sau_dm_qualification_company_qualification_id_foreign');
            $table->foreign('qualification_id')->references('id')->on('sau_dm_qualifications');
        });
    }
}
