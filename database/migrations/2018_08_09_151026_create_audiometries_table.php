<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudiometriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bm_audiometries', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->text('previews_events')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('employee_id');
            $table->string('work_zone_noise')->nullable();
            $table->string('exposition_level')->nullable();
            $table->integer('left_500');
            $table->integer('left_1000');
            $table->integer('left_2000');
            $table->integer('left_3000');
            $table->integer('left_4000');
            $table->integer('left_6000');
            $table->integer('left_8000');
            $table->integer('right_500');
            $table->integer('right_1000');
            $table->integer('right_2000');
            $table->integer('right_3000');
            $table->integer('right_4000');
            $table->integer('right_6000');
            $table->integer('right_8000');
            $table->string('left_clasification')->nullable();
            $table->string('right_clasification')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('observation')->nullable();
            $table->integer('test_score')->nullable();
            $table->string('epp')->nullable();
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
        Schema::dropIfExists('bm_audiometries');
    }
}
