<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincCie10CodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_cie10_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('category')->nullable();
            $table->string('description', 300)->nullable();
            $table->string('system')->nullable();
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
        Schema::dropIfExists('sau_reinc_cie10_codes');
    }
}
