<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsCausesAccidentsModifyDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_accidents_secondary_causes', function (Blueprint $table) {            
            $table->string('description')->nullable()->change();
            $table->unsignedInteger('section_id')->nullable();

            $table->foreign('section_id', 'aw_section_id_foreign')->references('id')->on('sau_aw_causes_sections')->onDelete('cascade');
        });

        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {         
            $table->string('description')->nullable()->change();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('item_id')->nullable();


            $table->foreign('category_id', 'aw_category_id_foreign')->references('id')->on('sau_aw_causes_section_category')->onDelete('cascade');
            $table->foreign('item_id', 'aw_item_id_foreign')->references('id')->on('sau_aw_causes_section_category_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_aw_accidents_secondary_causes', function (Blueprint $table) {            
            $table->dropForeign('aw_section_id_foreign');
            $table->dropColumn('section_id');
        });

        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {
            $table->dropForeign('aw_category_id_foreign');
            $table->dropForeign('aw_item_id_foreign');
            $table->dropColumn('category_id');
            $table->dropColumn('item_id');
        });
    }
}
