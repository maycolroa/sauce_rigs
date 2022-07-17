<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppMinimumStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_minimum_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('element_id');
            $table->unsignedInteger('location_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('element_id')->references('id')->on('sau_epp_elements')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('sau_epp_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_minimum_stock');
    }
}
