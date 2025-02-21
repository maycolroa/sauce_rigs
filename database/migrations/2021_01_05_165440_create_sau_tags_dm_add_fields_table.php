<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauTagsDmAddFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_tags_dm_add_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('field_id');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('sau_dm_additional_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_tags_dm_add_fields');
    }
}
