<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmCauseControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_cause_controls', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rm_cause_id');
            $table->text('controls');

            $table->foreign('rm_cause_id')->references('id')->on('sau_rm_causes')->onDelete('cascade'); 
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
        Schema::dropIfExists('sau_rm_cause_controls');
    }
}
