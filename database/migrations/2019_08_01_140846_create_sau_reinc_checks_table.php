<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('employee_id');
            $table->string('state')->default('ABIERTO');
            $table->string('disease_origin');
            $table->string('has_recommendations');
            $table->date('start_recommendations')->nullable();
            $table->date('end_recommendations')->nullable();
            $table->string('indefinite_recommendations')->nullable();
            $table->string('origin_recommendations')->nullable();
            $table->string('relocated')->nullable();
            $table->string('laterality')->nullable();
            $table->text('detail')->nullable();
            $table->date('monitoring_recommendations')->nullable();
            $table->string('in_process_origin');
            $table->string('process_origin_done')->nullable();
            $table->date('process_origin_done_date')->nullable();
            $table->string('emitter_origin')->nullable();
            $table->string('in_process_pcl');
            $table->string('process_pcl_done')->nullable();
            $table->date('process_pcl_done_date')->nullable();
            $table->double('pcl', 10, 2)->unsigned()->default(0)->nullable();
            $table->string('entity_rating_pcl')->nullable();
            $table->string('process_origin_file')->nullable();
            $table->string('process_origin_file_name')->nullable();
            $table->string('process_pcl_file')->nullable();
            $table->string('process_pcl_file_name')->nullable();
            $table->unsignedInteger('cie10_code_id');
            $table->unsignedInteger('restriction_id')->nullable();
            $table->string('has_restrictions');
            $table->unsignedInteger('relocated_regional_id')->nullable();
            $table->unsignedInteger('relocated_headquarter_id')->nullable();
            $table->unsignedInteger('relocated_process_id')->nullable();
            $table->unsignedInteger('relocated_position_id')->nullable();
            $table->date('date_controversy_origin_1')->nullable();
            $table->date('date_controversy_origin_2')->nullable();
            $table->date('date_controversy_pcl_1')->nullable();
            $table->date('date_controversy_pcl_2')->nullable();
            $table->string('emitter_controversy_origin_1')->nullable();
            $table->string('emitter_controversy_origin_2')->nullable();
            $table->string('emitter_controversy_pcl_1')->nullable();
            $table->string('emitter_controversy_pcl_2')->nullable();
            $table->string('malady_origin')->nullable();
            $table->string('eps_favorability_concept')->nullable();
            $table->string('case_classification')->nullable();
            $table->date('relocated_date')->nullable();
            $table->date('start_restrictions')->nullable();
            $table->date('end_restrictions')->nullable();
            $table->string('indefinite_restrictions')->nullable();
            $table->string('has_incapacitated')->nullable();
            $table->integer('incapacitated_days')->nullable();
            $table->date('incapacitated_last_extension')->nullable();
            $table->date('deadline')->nullable();
            $table->date('next_date_tracking')->nullable();
            $table->string('sve_associated')->nullable();
            $table->string('medical_certificate_ueac')->nullable();
            $table->string('relocated_type')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('sau_employees')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cie10_code_id')->references('id')->on('sau_reinc_cie10_codes')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('restriction_id')->references('id')->on('sau_reinc_restrictions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('relocated_regional_id')->references('id')->on('sau_employees_regionals')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('relocated_headquarter_id')->references('id')->on('sau_employees_headquarters')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('relocated_process_id')->references('id')->on('sau_employees_processes')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('relocated_position_id')->references('id')->on('sau_employees_positions')
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
        Schema::dropIfExists('sau_reinc_checks');
    }
}
