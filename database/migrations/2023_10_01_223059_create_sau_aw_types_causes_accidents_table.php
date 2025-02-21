<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAwTypesCausesAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_causes_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_name');
        });

        Schema::create('sau_aw_causes_section_category', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id');
            $table->string('category_name');

            $table->foreign('section_id')->references('id')->on('sau_aw_causes_sections')->onDelete('cascade');
        });

        Schema::create('sau_aw_causes_section_category_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('item_name');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('sau_aw_causes_section_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_aw_causes_section_category_items');
        Schema::dropIfExists('sau_aw_causes_section_category');
        Schema::dropIfExists('sau_aw_causes_sections');
    }
}
