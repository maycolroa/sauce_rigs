<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauKeywordCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_keyword_company', function (Blueprint $table) {
            $table->unsignedInteger('keyword_id');
            $table->unsignedInteger('company_id');
            $table->string('display_name');

            $table->foreign('keyword_id')->references('id')->on('sau_keywords')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_keyword_company');
    }
}
