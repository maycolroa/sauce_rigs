<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauBmMusculoskeletalAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_bm_musculoskeletal_analysis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('document_type', 2)->nullable();
            $table->string('patient_identification', 15)->nullable();
            $table->string('name')->nullable();
            $table->string('professional_identification', 15)->nullable();
            $table->string('professional')->nullable();
            $table->string('order', 25)->nullable();
            $table->date('date')->nullable();
            $table->integer('attention_code')->length(10)->nullable();
            $table->string('attention', 10)->nullable();
            $table->string('evaluation_type')->nullable();
            $table->string('evaluation_format')->nullable();
            $table->string('department')->nullable();
            $table->string('nit_company', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('nit_company_mission')->nullable();
            $table->string('company_mission')->nullable();
            $table->string('branch_office')->nullable();
            $table->string('sex', 10)->nullable();
            $table->integer('age')->length(3)->nullable();
            $table->string('etareo_group')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone_alternative', 20)->nullable();
            $table->string('eps')->nullable();
            $table->string('afp')->nullable();
            $table->integer('stratum')->length(2)->nullable();
            $table->integer('number_people_charge')->length(3)->nullable();
            $table->string('scholarship', 30)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('position')->nullable();
            $table->string('antiquity', 15)->nullable();
            $table->string('ant_atep_ep', 20)->nullable();
            $table->string('which_ant_atep_ep')->nullable();
            $table->string('exercise_habit', 3)->nullable();
            $table->string('exercise_frequency')->nullable();
            $table->string('liquor_habit', 3)->nullable();
            $table->string('liquor_frequency')->nullable();
            $table->string('exbebedor_habit', 3)->nullable();
            $table->string('liquor_suspension_time')->nullable();
            $table->string('cigarette_habit', 3)->nullable();
            $table->string('cigarette_frequency')->nullable();
            $table->string('habit_extra_smoker', 3)->nullable();
            $table->string('cigarrillo_suspension_time')->nullable();
            $table->text('activity_extra_labor')->nullable();
            $table->integer('pressure_systolic')->length(4)->nullable();
            $table->integer('pressure_diastolic')->length(4)->nullable();
            $table->float('weight')->nullable();
            $table->integer('size')->length(4)->nullable();
            $table->string('imc')->nullable();
            $table->string('imc_lassification')->nullable();
            $table->integer('abdominal_perimeter')->length(3)->nullable();
            $table->string('abdominal_perimeter_classification')->nullable();
            $table->string('diagnostic_code_1', 10)->nullable();
            $table->string('diagnostic_1')->nullable();
            $table->string('diagnostic_code_2', 10)->nullable();
            $table->string('diagnostic_2')->nullable();
            $table->string('diagnostic_code_3', 10)->nullable();
            $table->string('diagnostic_3')->nullable();
            $table->string('diagnostic_code_4', 10)->nullable();
            $table->string('diagnostic_4')->nullable();
            $table->string('diagnostic_code_5', 10)->nullable();
            $table->string('diagnostic_5')->nullable();
            $table->string('diagnostic_code_6', 10)->nullable();
            $table->string('diagnostic_6')->nullable();
            $table->string('diagnostic_code_7', 10)->nullable();
            $table->string('diagnostic_7')->nullable();
            $table->string('diagnostic_code_8', 10)->nullable();
            $table->string('diagnostic_8')->nullable();
            $table->string('diagnostic_code_9', 10)->nullable();
            $table->string('diagnostic_9')->nullable();
            $table->string('diagnostic_code_10', 10)->nullable();
            $table->string('diagnostic_10')->nullable();
            $table->string('diagnostic_code_11', 10)->nullable();
            $table->string('diagnostic_11')->nullable();
            $table->string('diagnostic_code_12', 10)->nullable();
            $table->string('diagnostic_12')->nullable();
            $table->string('diagnostic_code_13', 10)->nullable();
            $table->string('diagnostic_13')->nullable();
            $table->string('diagnostic_code_14', 10)->nullable();
            $table->string('diagnostic_14')->nullable();
            $table->string('diagnostic_code_15', 10)->nullable();
            $table->string('diagnostic_15')->nullable();
            $table->string('diagnostic_code_16', 10)->nullable();
            $table->string('diagnostic_16')->nullable();
            $table->string('diagnostic_code_17', 10)->nullable();
            $table->string('diagnostic_17')->nullable();
            $table->string('diagnostic_code_18', 10)->nullable();
            $table->string('diagnostic_18')->nullable();
            $table->string('cardiovascular_risk')->nullable();
            $table->string('osteomuscular_classification')->nullable();
            $table->string('osteomuscular_group')->nullable();
            $table->integer('age_risk')->length(4)->nullable();
            $table->integer('pathological_background_risks')->length(4)->nullable();
            $table->integer('extra_labor_activities_risk')->length(4)->nullable();
            $table->integer('sedentary_risk')->length(4)->nullable();
            $table->integer('imc_risk')->length(4)->nullable();
            $table->float('consolidated_personal_risk_punctuation')->nullable();
            $table->string('consolidated_personal_risk_criterion')->nullable();
            $table->string('prioritization_medical_criteria')->nullable();
            $table->string('concept')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('observations')->nullable();
            $table->text('restrictions')->nullable();
            $table->string('remission')->nullable();
            $table->string('authorization_access_information', 3)->nullable();
            $table->datetime('date_end')->nullable();
            $table->text('description_medical_exam')->nullable();
            $table->text('symptom')->nullable();
            $table->string('symptom_type')->nullable();
            $table->string('body_part')->nullable();
            $table->string('periodicity')->nullable();
            $table->string('workday', 3)->nullable();
            $table->text('symptomatology_observations')->nullable();
            $table->string('optometry')->nullable();
            $table->string('visiometry')->nullable();
            $table->string('audiometry')->nullable();
            $table->string('spirometry')->nullable();
            $table->text('tracing')->nullable();
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
        Schema::dropIfExists('sau_bm_musculoskeletal_analysis');
    }
}
