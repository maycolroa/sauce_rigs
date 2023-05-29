<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsReportDetail2SauDmReportHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_report_histories', function (Blueprint $table) {
            $table->text('qualification_text')->nullable();
            $table->text('regional_id')->nullable();
            $table->text('area_id')->nullable();
            $table->text('headquarter_id')->nullable();
            $table->text('process_id')->nullable();
            $table->text('nivel_probabilily')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_report_histories', function (Blueprint $table) {
            $table->dropColumn('qualification_text');
            $table->dropColumn('regional_id');
            $table->dropColumn('area_id');
            $table->dropColumn('headquarter_id');
            $table->dropColumn('process_id');
            $table->dropColumn('nivel_probabilily');
        });
    }
}
