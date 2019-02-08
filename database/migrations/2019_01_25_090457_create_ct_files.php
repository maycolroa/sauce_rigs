<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCtFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file', 100);
            $table->unsignedInteger('information_id');
            $table->timestamp('expires_date');
            $table->timestamps();

            $table->foreign('information_id')->references('id')->on('sau_ct_information_contract_lessee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_files');
    }
}
