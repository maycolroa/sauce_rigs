<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDocumentsLegalsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_documents_legals_user', function (Blueprint $table) {
            $table->unsignedInteger('document_legal_id');
            $table->unsignedInteger('user_id');
            
            $table->foreign('document_legal_id','document_legal_id_foreign')->references('id')->on('sau_documents_legals')->onDelete('cascade');
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
        Schema::dropIfExists('sau_documents_legals_user');
    }
}
