<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAwCausesSectionCategoryCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_causes_section_category_company', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('category_default_id');
            $table->string('category_name');
            $table->unsignedInteger('company_id');
            $table->timestamps();

            $table->foreign('section_id')->references('id')->on('sau_aw_causes_sections')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('category_default_id', 'section_category_default_id_foreign')->references('id')->on('sau_aw_causes_section_category')->onDelete('cascade');
        });

        Schema::create('sau_aw_causes_section_category_items_company', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('item_name');
            $table->unsignedInteger('company_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('sau_aw_causes_section_category_company')->onDelete('cascade');
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
        Schema::dropIfExists('sau_aw_causes_section_category_items_company');
        Schema::dropIfExists('sau_aw_causes_section_category_company');
    }
}
