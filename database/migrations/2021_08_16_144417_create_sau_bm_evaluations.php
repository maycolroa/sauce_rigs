<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmEvaluations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_creator_id');
            $table->boolean('in_edit')->default(false);            
            $table->string('user_edit')->nullable();           
            $table->dateTime('time_edit')->nullable();
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('sau_modules')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_creator_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_bm_evaluations');
    }
}
