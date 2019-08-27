<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAbsenReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_absen_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_show');
            $table->string('name_report');
            $table->string('user');
            $table->string('site');
            $table->unsignedInteger('company_id');
            $table->tinyInteger('state');
            $table->tinyInteger('type');
            $table->tinyInteger('es_bsc');
            $table->unsignedInteger('module_id')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')
                ->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('module_id')->references('id')->on('sau_absen_reports_modules')
                ->onUpdate('restrict')->onDelete('restrict');    

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_absen_reports');
    }
}
