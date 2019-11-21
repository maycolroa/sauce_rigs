<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('check_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('file');
            $table->string('file_name');

            $table->foreign('check_id')->references('id')->on('sau_reinc_checks')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')
            ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_reinc_files');
    }
}
