<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtItemsStandard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_items_standard', function (Blueprint $table) {
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('standard_id');

            $table->foreign('item_id')->references('id')->on('sau_ct_section_category_items')->onDelete('cascade');
            $table->foreign('standard_id')->references('id')->on('sau_ct_standard_classification')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_items_standard');
    }
}
