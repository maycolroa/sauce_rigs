<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmSauReincLaborMonitoringHasMonitoringContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_labor_monitorings', function (Blueprint $table) {
            $table->string('has_monitoring_content')->nullable()->after('check_id');
            $table->string('productivity')->nullable()->after('has_monitoring_content');                 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_labor_monitorings', function (Blueprint $table) {
            $table->dropColumn('has_monitoring_content');
            $table->dropColumn('productivity');
        });
    }
}
