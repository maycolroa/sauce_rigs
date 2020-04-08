<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtTrainingQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_training_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('training_id');
            $table->text('description');
            $table->unsignedInteger('type_question_id');
            $table->text('answer_options');
            $table->string('value_question');

            $table->foreign('training_id')->references('id')->on('sau_ct_trainings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('type_question_id')->references('id')->on('sau_ct_training_types_questions')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('sau_ct_training_questions');
    }
}
