<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('code');
            $table->string('name');
            $table->text('description');
            $table->text('type');
            $table->string('mark');
            $table->text('applicable_standard');
            $table->text('observations')->nullable();
            $table->text('operating_instructions')->nullable();
            $table->boolean('state');
            $table->boolean('reusable');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_elements');
    }
}
