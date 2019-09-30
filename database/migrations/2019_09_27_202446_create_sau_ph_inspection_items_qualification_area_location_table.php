<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhInspectionItemsQualificationAreaLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_inspection_items_qualification_area_location', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('qualification_id');
            $table->unsignedInteger('employee_headquarter_id');
            $table->unsignedInteger('employee_area_id');
            $table->unsignedInteger('qualifier_id');
            $table->text('find');
            $table->dateTime('qualification_date');
            $table->string('photo_1',255)->nullable();
            $table->string('photo_2',255)->nullable();

            $table->unique(['item_id','employee_headquarter_id','employee_area_id','qualification_date'],'items_qualifications_headquarters_areas_date');
            $table->foreign('employee_headquarter_id','headquarter_id_foreign')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
            $table->foreign('employee_area_id','area_id_foreign')->references('id')->on('sau_employees_areas')->onDelete('cascade');
            $table->foreign('qualification_id','qualification_id_foreign')->references('id')->on('sau_ct_qualifications')->onDelete('cascade');
            $table->foreign('item_id','item_id_foreign')->references('id')->on('sau_ph_inspections_section_items')->onDelete('cascade');
            $table->foreign('qualifier_id','qualifier_id_foreign')->references('id')->on('sau_users')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_inspection_items_qualification_area_location');
    }
}
