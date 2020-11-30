<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionQualificationRepeatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_qualification_repeat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('user_id');
            $table->string('regional');
            $table->string('headquarter')->nullable();
            $table->string('process')->nullable();
            $table->string('area')->nullable();
            $table->text('fields_adds')->nullable();
            $table->text('send_emails')->nullable();
            $table->dateTime('qualification_date');
            $table->dateTime('repeat_date');
            $table->timestamps();

            $table->foreign('inspection_id')->references('id')->on('sau_ph_inspections')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspection_qualification_repeat');
    }
}
