<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesFilesPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_form_accidents_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('file');
            $table->string('type');
            $table->unsignedInteger('form_accident_id');
            $table->timestamps();

            $table->foreign('form_accident_id')->references('id')->on('sau_aw_form_accidents')->onDelete('cascade');
        });

        Schema::create('sau_aw_form_accidents_people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('position');
            $table->string('type_document');
            $table->string('document');
            $table->string('rol')->nullable();
            $table->unsignedInteger('form_accident_id');

            $table->foreign('form_accident_id')->references('id')->on('sau_aw_form_accidents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_aw_form_accidents_files');
        Schema::dropIfExists('sau_aw_form_accidents_people');
    }
}
