<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmTagCategoriesRisksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_tag_categories_risks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('company_id');

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
        Schema::dropIfExists('sau_rm_tag_categories_risks');
    }
}
