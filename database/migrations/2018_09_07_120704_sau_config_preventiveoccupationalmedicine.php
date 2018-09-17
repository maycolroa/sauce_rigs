<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SauConfigPreventiveoccupationalmedicine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_configuration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('value');
            $table->text('observation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_configuration');
    }
}
