<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhQualificationInspectionFirmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_qualification_inspection_firm', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('identification');
            $table->text('image');
            $table->dateTime('qualification_date');
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
        Schema::dropIfExists('sau_ph_qualification_inspection_firm');
    }
}
