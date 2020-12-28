<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmAddFieldsValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_add_fields_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_id');
            $table->unsignedInteger('danger_matrix_id');
            $table->text('value');
            $table->timestamps();

            $table->foreign('field_id', 'field_id_dm')->references('id')->on('sau_dm_additional_fields')->onDelete('cascade');
            $table->foreign('danger_matrix_id')->references('id')->on('sau_dangers_matrix')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_add_fields_values');
    }
}
