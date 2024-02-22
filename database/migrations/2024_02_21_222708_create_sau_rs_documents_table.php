<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->unsignedInteger('employee_position_id');
            $table->timestamps();

            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_position_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('position_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('position_id')->references('id')->on('sau_rs_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_position_documents');
        Schema::dropIfExists('sau_rs_positions');
    }
}
