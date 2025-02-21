<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmCausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_causes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rm_subprocess_risk_id');
            $table->text('cause');

            $table->foreign('rm_subprocess_risk_id')->references('id')->on('sau_rm_subprocess_risk')->onDelete('cascade'); 

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
        Schema::dropIfExists('sau_rm_causes');
    }
}
