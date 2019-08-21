<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFileUploadContractsLeesse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name", 255);
            $table->string("file", 255);
            $table->date("expirationDate")->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign("user_id")->references('id')->on('sau_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_file_upload_contracts_leesse');
    }
}
