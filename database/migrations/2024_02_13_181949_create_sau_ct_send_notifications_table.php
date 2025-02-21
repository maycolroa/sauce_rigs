<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtSendNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_send_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('subject');
            $table->text('body');
            $table->date('date_send')->nullable();
            $table->time('hour')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('send')->default(false);

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            
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
        Schema::dropIfExists('sau_ct_send_notifications');
    }
}
