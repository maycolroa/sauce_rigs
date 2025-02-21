<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeItemSauPhInspectionSectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspection_section_items', function (Blueprint $table) {
            $table->unsignedInteger('type_id')->after('partial_value')->nullable();
            $table->text('values')->nullable();

            $table->foreign('type_id')->references('id')->on('sau_ph_inspetions_type_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_inspection_section_items', function (Blueprint $table) {            
            $table->dropForeign('sau_ph_inspection_section_items_type_id_foreign');
            $table->dropColumn('type_id');
        });
    }
}
