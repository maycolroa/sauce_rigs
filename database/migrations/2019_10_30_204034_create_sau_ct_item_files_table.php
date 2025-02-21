<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtItemFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_evaluation_item_files', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('item_id');
                $table->unsignedInteger('evaluation_id');
                $table->string('file');
                $table->timestamps();

                $table->foreign('item_id')->references('id')->on('sau_ct_items')->onDelete('cascade');
                $table->foreign('evaluation_id')->references('id')->on('sau_ct_evaluation_contract')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_evaluation_item_files');
    }
}
