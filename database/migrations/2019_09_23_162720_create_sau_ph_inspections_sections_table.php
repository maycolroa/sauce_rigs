<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionsSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('inspection_id');
            $table->timestamps();

            $table->foreign('inspection_id')->references('id')->on('sau_ph_inspections')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspection_sections');
    }
}
