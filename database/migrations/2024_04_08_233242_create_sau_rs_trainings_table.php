<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->unsignedInteger('creator_user');
            $table->unsignedInteger('modifier_user');
            $table->integer('number_questions_show');
            $table->integer('min_calification');
            $table->integer('max_calification');
            $table->integer('number_attemps');            
            $table->string('active')->default('NO'); 

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreign('creator_user')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modifier_user')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('sau_rs_training_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('training_id');
            $table->text('description');
            $table->unsignedInteger('type_question_id');
            $table->text('answer_options');
            $table->integer('value_question')->default(1);

            $table->foreign('training_id')->references('id')->on('sau_rs_trainings')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('type_question_id')->references('id')->on('sau_ct_training_types_questions')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('sau_rs_training_position', function (Blueprint $table) {
            $table->unsignedInteger("training_id");
            $table->unsignedInteger("employee_position_id");

            $table->foreign('training_id')->references('id')->on('sau_rs_trainings')->onDelete('cascade');
            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions')->onDelete('cascade');
        });


        Schema::create('sau_rs_training_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('training_id');
            $table->string('type')->default('Archivo');
            $table->string('name');
            $table->string('type_file')->nullable();
            $table->string('file')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('sau_rs_trainings')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_training_employee_send', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("training_id");
            $table->unsignedInteger("employee_id");

            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('sau_rs_trainings')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('sau_employees')->onDelete('cascade');
        });

        Schema::create('sau_rs_training_employee_attempts', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('attempt');
            $table->unsignedInteger('training_id');
            $table->unsignedInteger('employee_id');
            $table->string('state');

            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('sau_rs_trainings')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('sau_employees')->onDelete('cascade');
        });

        Schema::create('sau_rs_training_employee_questions_answers', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->unsignedInteger('question_id');
            $table->unsignedInteger('attempt_id');
            $table->text('answers');
            $table->boolean('correct')->default(false);

            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('sau_rs_training_questions')->onDelete('cascade');
            $table->foreign('attempt_id')->references('id')->on('sau_rs_training_employee_attempts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_training_employee_questions_answers');
        Schema::dropIfExists('sau_rs_training_employee_attempts');
        Schema::dropIfExists('sau_rs_training_employee_send');
        Schema::dropIfExists('sau_rs_training_files');
        Schema::dropIfExists('sau_rs_training_position');
        Schema::dropIfExists('sau_rs_training_questions');
        Schema::dropIfExists('sau_rs_trainings');
    }
}
