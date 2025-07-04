<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppTagsApplicableStandardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_tags_applicable_standard', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('system')->default(false);
            $table->unsignedInteger('company_id')->nullable();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('sau_epp_tags_applicable_standard');
    }
}
