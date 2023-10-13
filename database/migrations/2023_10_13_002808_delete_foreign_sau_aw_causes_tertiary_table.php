<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteForeignSauAwCausesTertiaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {
            $table->dropForeign('aw_category_id_foreign');
            $table->dropForeign('aw_item_id_foreign');
            $table->dropColumn('category_id');
            $table->dropColumn('item_id');
        });

        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {         
            //$table->string('description')->nullable()->change();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('item_id')->nullable();


            $table->foreign('category_id', 'aw_category_id_foreign')->references('id')->on('sau_aw_causes_section_category_company')->onDelete('cascade');
            $table->foreign('item_id', 'aw_item_id_foreign')->references('id')->on('sau_aw_causes_section_category_items_company')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {
            $table->dropForeign('aw_category_id_foreign');
            $table->dropForeign('aw_item_id_foreign');
            $table->dropColumn('category_id');
            $table->dropColumn('item_id');
        });

        Schema::table('sau_aw_accidents_tertiary_causes', function (Blueprint $table) {         
            //$table->string('description')->nullable()->change();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('item_id')->nullable();


            $table->foreign('category_id', 'aw_category_id_foreign')->references('id')->on('sau_aw_causes_section_category_company')->onDelete('cascade');
            $table->foreign('item_id', 'aw_item_id_foreign')->references('id')->on('sau_aw_causes_section_category_items_company')->onDelete('cascade');
        });
    }
}
