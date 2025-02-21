<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmRespiratoryAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_respiratory_analysis', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('company_id');
            $table->string('patient_identification');
            $table->string('name')->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('deal')->nullable();            
            $table->string('regional')->nullable();
            $table->date('date_of_birth')->nullable();            
            $table->integer('age')->nullable();
            $table->date('income_date')->nullable();
            $table->integer('antiquity')->nullable();
            $table->string('area')->nullable();
            $table->string('position')->nullable();
            $table->string('habits')->nullable();
            $table->string('history_of_respiratory_pathologies')->nullable();
            $table->string('measurement_date')->nullable();
            $table->string('mg_m3_concentration')->nullable();
            $table->string('ir')->nullable();
            $table->string('type_of_exam')->nullable();
            $table->string('year_of_spirometry')->nullable();
            $table->string('spirometry')->nullable();
            $table->date('date_of_realization')->nullable();
            $table->string('symptomatology')->nullable();
            $table->string('cvf_average_percentage')->nullable();
            $table->string('vef1_average_percentage')->nullable();
            $table->string('vef1_cvf_average')->nullable();
            $table->string('fef_25_75_porcentage')->nullable();
            $table->string('interpretation')->nullable();
            $table->string('type_of_exam_2')->nullable();
            $table->date('date_of_realization_2')->nullable();
            $table->string('rx_oit')->nullable();
            $table->string('quality')->nullable();
            $table->string('yes_1')->nullable();
            $table->string('not_1')->nullable();
            $table->text('answer_yes_describe')->nullable();
            $table->string('yes_2')->nullable();
            $table->string('not_2')->nullable();
            $table->text('answer_yes_describe_2')->nullable();
            $table->string('other_abnormalities')->nullable();
            $table->string('fully_negative')->nullable();
            $table->text('observation')->nullable();
            $table->string('breathing_problems')->nullable();
            $table->string('classification_according_to_ats')->nullable();
            $table->string('ats_obstruction_classification')->nullable();
            $table->string('ats_restrictive_classification')->nullable();
            $table->string('state')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_bm_respiratory_analysis');
    }
}