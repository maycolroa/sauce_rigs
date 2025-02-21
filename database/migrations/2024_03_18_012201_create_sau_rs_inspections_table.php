<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_inspections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('type_id')->default(1);
            $table->string('state')->default('SI');
            $table->double('fullfilment_parcial')->default(0.5);
            $table->string('version')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('sau_ph_type_inspections')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspection_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('inspection_id');
            $table->timestamps();

            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspection_section_items', function (Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            $table->integer('inspection_section_id')->unsigned();
            $table->double('compliance_value')->nullable();
            $table->double('partial_value')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->text('values')->nullable();

            $table->timestamps();

            $table->foreign('inspection_section_id')->references('id')->on('sau_rs_inspection_sections')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('sau_ph_inspetions_type_items')->onDelete('cascade');
        });


        Schema::create('sau_rs_inspection_regional', function (Blueprint $table) {
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('employee_regional_id');
            
            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onDelete('cascade');
            $table->foreign('employee_regional_id')->references('id')->on('sau_employees_regionals')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspection_headquarter', function (Blueprint $table) {
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('employee_headquarter_id');
            
            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onDelete('cascade');
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspection_process', function (Blueprint $table) {
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('employee_process_id');
            
            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspection_area', function (Blueprint $table) {
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('employee_area_id');
            
            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onDelete('cascade');
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas')->onDelete('cascade');
        });

        Schema::create('sau_rs_inspections_additional_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inspection_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onDelete('cascade');
        });

        Schema::create('sau_rs_qualification_masive_company', function (Blueprint $table) {
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
        Schema::dropIfExists('sau_rs_inspection_regional');
        Schema::dropIfExists('sau_rs_inspection_headquarter');
        Schema::dropIfExists('sau_rs_inspection_process');
        Schema::dropIfExists('sau_rs_inspection_area');
        Schema::dropIfExists('sau_rs_inspections_additional_fields');
        Schema::dropIfExists('sau_rs_qualification_masive_company');
        Schema::dropIfExists('sau_rs_inspection_section_items');
        Schema::dropIfExists('sau_rs_inspection_sections');
        Schema::dropIfExists('sau_rs_inspections');
    }
}
