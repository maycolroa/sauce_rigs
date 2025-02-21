<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->unsignedInteger('creator_user');
            $table->unsignedInteger('modifier_user');
            $table->integer('number_questions_show');
            $table->integer('min_calification');
            $table->integer('max_calification');
            $table->integer('number_attemps');
            $table->string('file')->nullable();
            
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreign('creator_user')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('modifier_user')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('sau_ct_trainings');
    }
}
