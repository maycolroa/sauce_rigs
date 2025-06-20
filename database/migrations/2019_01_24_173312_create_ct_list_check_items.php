<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCtListCheckItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_list_check_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('list_check_id');
            $table->integer('state_quelified');
            $table->timestamp('execution_date');
            $table->timestamps();

            $table->foreign('list_check_id')->references('id')->on('sau_ct_list_check')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_list_check_items');
    }
}
