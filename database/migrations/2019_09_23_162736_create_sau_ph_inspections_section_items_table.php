<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionsSectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_section_items', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->integer('inspection_section_id')->unsigned();
            $table->timestamps();

            $table->foreign('inspection_section_id')->references('id')->on('sau_ph_inspection_sections')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspection_section_items');
    }
}
