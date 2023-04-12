<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauNewslettersSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_newsletters_sends', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->string('image');
            $table->string('image_name');
            $table->date('date_send')->nullable();
            $table->time('hour')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('send')->default(false);
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
        Schema::dropIfExists('sau_newsletters_sends');
    }
}
