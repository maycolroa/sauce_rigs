<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionsAdditionalFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspections_additional_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inspection_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('inspection_id')->references('id')->on('sau_ph_inspections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspections_additional_fields');
    }
}
