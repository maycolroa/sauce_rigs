<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SauReincMedicalMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_medical_monitorings', function (Blueprint $table) {
            $table->increments('id');
            $table->date('set_at');
            $table->string('conclusion');
            $table->unsignedInteger('check_id');
            $table->timestamps();

            $table->foreign('check_id')->references('id')->on('sau_reinc_checks')
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
        Schema::dropIfExists('sau_reinc_medical_monitorings');
    }
}
