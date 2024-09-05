<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionQualificationStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_qualification_state', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('qualification_date');
            $table->string('state')->nullable();
            $table->text('motive')->nullable();
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
        Schema::dropIfExists('sau_ph_inspection_qualification_state');
    }
}
