<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAbsenFileUploadUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_absen_file_upload_user', function (Blueprint $table) {
            $table->unsignedInteger('file_upload_id');
            $table->unsignedInteger('user_id');
            $table->foreign('file_upload_id')->references('id')->on('sau_absen_file_upload')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_absen_file_upload_user');
    }
}
