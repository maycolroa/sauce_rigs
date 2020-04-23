<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCorrectSauTrainingEmployeeQuestionsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_training_employee_questions_answers', function (Blueprint $table) {
            $table->boolean('correct')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_training_employee_questions_answers', function (Blueprint $table) {
            $table->dropColumn('correct');
        });
    }
}
