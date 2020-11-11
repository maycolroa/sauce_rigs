<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauTagsDmHistoryChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_tags_dm_history_change', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('company_id');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_tags_dm_history_change');
    }
}
