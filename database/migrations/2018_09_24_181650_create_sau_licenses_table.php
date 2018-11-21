<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->date('started_at');
            $table->date('ended_at');
            $table->integer('module_id')->unsigned();
            $table->integer('company_id')->unsigned();
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('sau_modules');
            $table->foreign('company_id')->references('id')->on('sau_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_licenses');
    }
}
