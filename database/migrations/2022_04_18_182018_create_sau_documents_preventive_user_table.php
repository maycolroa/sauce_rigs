<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDocumentsPreventiveUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_documents_preventive_user', function (Blueprint $table) {
            $table->unsignedInteger('document_preventive_id');
            $table->unsignedInteger('user_id');
            
            $table->foreign('document_preventive_id','document_preventive_id_foreign')->references('id')->on('sau_documents_preventive')->onDelete('cascade');
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
        Schema::dropIfExists('sau_documents_preventive_user');
    }
}
