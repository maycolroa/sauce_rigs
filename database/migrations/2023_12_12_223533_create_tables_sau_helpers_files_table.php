<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesSauHelpersFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_helpers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('sau_modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });

        Schema::create('sau_helper_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('helper_id');
            $table->string('file');
            $table->timestamps();

            $table->foreign('helper_id')->references('id')->on('sau_helpers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_helper_files');
        Schema::dropIfExists('sau_helpers');
    }
}
