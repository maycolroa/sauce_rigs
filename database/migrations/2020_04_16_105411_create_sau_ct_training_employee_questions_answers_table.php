<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtTrainingEmployeeQuestionsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_training_employee_questions_answers', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->unsignedInteger('question_id');
            $table->unsignedInteger('attempt_id');
            $table->text('answers');

            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('sau_ct_trainings')->onDelete('cascade');
            $table->foreign('attempt_id')->references('id')->on('sau_ct_training_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_training_employee_questions_answers');
    }
}
