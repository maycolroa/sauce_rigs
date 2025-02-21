<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtInformThemeItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_inform_theme_item', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evaluation_theme_id');
            $table->string('description');

            $table->foreign('evaluation_theme_id')->references('id')->on('sau_ct_informs_themes')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_inform_theme_item');
    }
}
