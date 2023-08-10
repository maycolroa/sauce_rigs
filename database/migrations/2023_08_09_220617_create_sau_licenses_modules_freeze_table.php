<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLicensesModulesFreezeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_license_module_freeze', function (Blueprint $table) {
            $table->unsignedInteger('license_id');
            $table->unsignedInteger('module_id');

            $table->foreign('license_id')->references('id')->on('sau_licenses');
            $table->foreign('module_id')->references('id')->on('sau_modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_license_module_freeze');
    }
}
