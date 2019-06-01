<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_laws', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('law_number');
            $table->string('apply_system');
            $table->integer('law_year');
            $table->unsignedInteger('law_type_id');
            $table->text('description');
            $table->text('observations');
            $table->unsignedInteger('risk_aspect_id');
            $table->unsignedInteger('entity_id');            
            $table->unsignedInteger('sst_risk_id');
            $table->string('repealed');
            $table->string('file');
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
        Schema::dropIfExists('sau_lm_laws');
    }
}
