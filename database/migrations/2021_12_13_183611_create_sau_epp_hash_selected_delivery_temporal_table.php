<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppHashSelectedDeliveryTemporalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_hash_selected_delivery_temporal', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('element_id');
            $table->integer('location_id');
            $table->string('hash');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_hash_selected_delivery_temporal');
    }
}
