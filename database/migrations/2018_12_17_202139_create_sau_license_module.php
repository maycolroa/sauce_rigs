<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLicenseModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_license_module', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('license_id');
            $table->unsignedInteger('module_id');
            $table->timestamps();

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
        Schema::dropIfExists('sau_license_module');
    }
}
