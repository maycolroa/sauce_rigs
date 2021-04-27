<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmRisksMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_risks_matrix', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('employee_regional_id')->nullable();
            $table->unsignedInteger('employee_headquarter_id')->nullable();
            $table->unsignedInteger('employee_area_id')->nullable();
            $table->unsignedInteger('employee_process_id')->nullable();
            $table->text('participants')->nullable();
            $table->boolean('approved')->default(false);

            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('company_id')->references('id')->on('sau_companies');

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
        Schema::dropIfExists('sau_rm_risks_matrix');
    }
}
