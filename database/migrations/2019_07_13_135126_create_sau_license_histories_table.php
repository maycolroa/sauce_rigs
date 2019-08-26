<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLicenseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_license_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('license_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('license_id')->references('id')->on('sau_licenses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_license_histories');
    }
}
