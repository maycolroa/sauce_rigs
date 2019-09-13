<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmReportHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_report_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->integer('year');
            $table->integer('month');
            $table->string('regional')->nullable();
            $table->string('area')->nullable();
            $table->string('headquarter')->nullable();
            $table->string('process')->nullable();
            $table->string('macroprocess')->nullable();
            $table->text('qualification')->nullable();
            $table->string('type_configuration')->nullable();
            $table->text('danger')->nullable();
            $table->text('danger_description')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_report_histories');
    }
}
