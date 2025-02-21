<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanyIdSauDmQualificationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_dm_qualifications_histories', function (Blueprint $table) {            
        $table->unsignedInteger('company_id')->nullable()->after('id');

        $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sau_dm_qualifications_histories', function (Blueprint $table) {            
        $table->dropColumn('company_id');
        });
    }
}
