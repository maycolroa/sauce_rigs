<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDocumentSecurityUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_document_security_users', function (Blueprint $table) {
            $table->unsignedInteger('document_security_id');
            $table->unsignedInteger('user_id');
            
            $table->foreign('document_security_id','document_security_id_foreign')->references('id')->on('sau_documents_security')->onDelete('cascade');
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
        Schema::dropIfExists('sau_document_security_users');
    }
}
