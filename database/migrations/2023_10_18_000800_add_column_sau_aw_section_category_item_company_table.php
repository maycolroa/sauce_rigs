<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauAwSectionCategoryItemCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_causes_section_category_items_company', function (Blueprint $table) {
            $table->boolean('category_default')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_aw_causes_section_category_items_company', function (Blueprint $table) {
            $table->dropColumn('category_default');
        });
    }
}
