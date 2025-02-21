<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsReportDetailSauDmReportHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_report_histories', function (Blueprint $table) {
            $table->text('activity')->nullable();
            $table->text('type_activity')->nullable();
            $table->text('participants')->nullable();
            $table->text('name_matrix')->nullable();
            $table->text('generating_source')->nullable();
            $table->text('existing_controls_engineering_controls')->nullable();
            $table->text('existing_controls_substitution')->nullable();
            $table->text('existing_controls_warning_signage')->nullable();
            $table->text('existing_controls_administrative_controls')->nullable();
            $table->text('existing_controls_epp')->nullable();
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
            $table->dropColumn('activity');
            $table->dropColumn('type_activity');
            $table->dropColumn('participants');
            $table->dropColumn('name_matrix');
            $table->dropColumn('generating_source');
            $table->dropColumn('existing_controls_engineering_controls');
            $table->dropColumn('existing_controls_substitution');
            $table->dropColumn('existing_controls_warning_signage');
            $table->dropColumn('existing_controls_administrative_controls');
            $table->dropColumn('existing_controls_epp');
        });
    }
}
