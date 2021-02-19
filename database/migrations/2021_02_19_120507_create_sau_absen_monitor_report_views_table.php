<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAbsenMonitorReportViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_absen_monitor_report_views', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('report_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('sau_absen_reports')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_absen_monitor_report_views');
    }
}
