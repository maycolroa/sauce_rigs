<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTransferElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_transfer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transfer_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('location_origin_id');
            $table->unsignedInteger('location_destiny_id');
            $table->unsignedInteger('element_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('sau_epp_transfers')->onDelete('cascade');
            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onDelete('cascade');
            $table->foreign('location_origin_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('location_destiny_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_transfer_details');
    }
}
