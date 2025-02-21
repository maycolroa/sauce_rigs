<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhAdditionalFieldsValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspections_additional_fields_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_id');
            $table->text('value');
            $table->dateTime('qualification_date');
            $table->timestamps();

            $table->foreign('field_id')->references('id')->on('sau_ph_inspections_additional_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspections_additional_fields_values');
    }
}
