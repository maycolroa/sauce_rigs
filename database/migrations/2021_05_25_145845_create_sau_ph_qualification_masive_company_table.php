<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhQualificationMasiveCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_qualification_masive_company', function (Blueprint $table) {
            $table->unsignedInteger('qualification_id');
            $table->unsignedInteger('company_id');

            $table->foreign('qualification_id')->references('id')->on('sau_ph_qualifications_inspections')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('sau_ph_qualification_masive_company');
    }
}
