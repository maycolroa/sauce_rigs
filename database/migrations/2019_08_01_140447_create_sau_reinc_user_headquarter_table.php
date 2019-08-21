<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincUserHeadquarterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_user_headquarter', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("headquarter_id");

            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('headquarter_id')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_reinc_user_headquarter');
    }
}
