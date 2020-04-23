<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteForeingSauCtQuestionsAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_training_employee_questions_answers', function (Blueprint $table) {

            $table->dropForeign('sau_ct_training_employee_questions_answers_question_id_foreign');

            $table->foreign('question_id')
                ->references('id')
                ->on('sau_ct_training_questions')
                ->onDelete('cascade');

            $table->dropForeign('sau_ct_training_employee_questions_answers_attempt_id_foreign');

            $table->foreign('attempt_id')
                    ->references('id')
                    ->on('sau_ct_training_employee_attempts')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
