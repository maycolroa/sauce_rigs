<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsInspectionsQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_inspections_qualified', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('inspection_id');
            $table->unsignedInteger('qualifier_id');
            $table->unsignedInteger('employee_regional_id');
            $table->unsignedInteger('employee_headquarter_id')->nullable();
            $table->unsignedInteger('employee_process_id')->nullable();
            $table->unsignedInteger('employee_area_id')->nullable();
            $table->dateTime('qualification_date');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('inspection_id')->references('id')->on('sau_rs_inspections')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('qualifier_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onDelete('cascade');
            $table->foreign('employee_regional_id','regional_id_rs_foreign')->references('id')->on('sau_employees_regionals')->onDelete('cascade');
            $table->foreign('employee_headquarter_id','headquarter_id_rs_foreign')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
            $table->foreign('employee_process_id','process_id_rs_foreign')->references('id')->on('sau_employees_processes')->onDelete('cascade');
            $table->foreign('employee_area_id','employee_area_id_rs_foreign')->references('id')->on('sau_employees_areas')->onDelete('cascade');
        });

        Schema::create('sau_rs_images_api', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->integer('type');
            $table->string('hash');
            $table->timestamps();
        });

        Schema::create('sau_rs_inspection_items_qualifications_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('inspection_qualification_id');
            $table->unsignedInteger('theme_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('qualification_id')->default(3);
            $table->text('qualify')->nullable();
            $table->text('find')->nullable();
            $table->string('level_risk')->nullable();
            $table->string('photo_1',255)->nullable();
            $table->string('photo_2',255)->nullable();


            $table->foreign('inspection_qualification_id', 'inspection_qualification_id_rs_foreign')->references('id')->on('sau_rs_inspections_qualified')->onDelete('cascade');

            $table->foreign('qualification_id','sau_qualification_id_rs_foreign')->references('id')->on('sau_ph_qualifications_inspections')->onDelete('cascade');

            $table->foreign('theme_id','sau_theme_id_rs_foreign')->references('id')->on('sau_rs_inspection_sections')->onDelete('cascade');
            $table->foreign('item_id','sau_item_id_rs_foreign')->references('id')->on('sau_rs_inspection_section_items')->onDelete('cascade');
            
        });

        Schema::create('sau_rs_qualification_inspection_firm', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('inspection_qualification_id');
            $table->text('name');
            $table->text('identification');
            $table->text('image');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('inspection_qualification_id', 'inspection_qualification_id_rs_firm_foreign')->references('id')->on('sau_rs_inspections_qualified')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_inspection_items_qualifications_locations');
        Schema::dropIfExists('sau_rs_qualification_inspection_firm');
        Schema::dropIfExists('sau_rs_inspections_qualified');
        Schema::dropIfExists('sau_rs_images_api');
    }
}
