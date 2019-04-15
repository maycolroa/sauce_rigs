<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileItemContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_file_item_contract', function (Blueprint $table) {
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('file_id');
            $table->foreign('item_id')->references('id')->on('sau_ct_section_category_items')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('sau_ct_file_upload_contracts_leesse')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_file_item_contract');
    }
}
