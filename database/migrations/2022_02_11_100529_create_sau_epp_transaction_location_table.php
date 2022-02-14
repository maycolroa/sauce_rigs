<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransactionLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('location_origin_id');
            $table->unsignedInteger('location_destiny_id');
            $table->string('state');
            $table->timestamps();

            $table->foreign('location_origin_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('location_destiny_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transfers');
    }
}
