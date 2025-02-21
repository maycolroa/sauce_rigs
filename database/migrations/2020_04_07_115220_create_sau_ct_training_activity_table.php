<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtTrainingActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_training_activity', function (Blueprint $table) {
            $table->unsignedInteger("training_id");
            $table->unsignedInteger("activity_id");

            $table->foreign('training_id')->references('id')->on('sau_ct_trainings')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('sau_ct_activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_training_activity');
    }
}
