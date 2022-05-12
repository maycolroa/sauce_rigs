<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_form_accidents_parts_body', function (Blueprint $table) {
            $table->integer('part_body_id')->unsigned();
            $table->integer('form_accident_id')->unsigned();

            $table->foreign('part_body_id')->references('id')->on('sau_aw_parts_body')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('form_accident_id')->references('id')->on('sau_aw_form_accidents')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_aw_form_accidents_types_lesion', function (Blueprint $table) {
            $table->integer('form_accident_id')->unsigned();
            $table->integer('type_lesion_id')->unsigned();

            $table->foreign('form_accident_id')->references('id')->on('sau_aw_form_accidents')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('type_lesion_id')->references('id')->on('sau_aw_types_lesion')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_aw_form_accidents_parts_body');
        Schema::dropIfExists('sau_aw_form_accidents_types_lesion');
    }
}
