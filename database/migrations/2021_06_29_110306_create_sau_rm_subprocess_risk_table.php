<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRmSubprocessRiskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rm_subprocess_risk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rm_subprocess_id');
            $table->unsignedInteger('risk_id');
            $table->integer('risk_sequence');
            $table->integer('economic');
            $table->integer('quality_care_patient_safety');
            $table->integer('reputational');
            $table->integer('legal_regulatory');
            $table->integer('environmental');
            $table->integer('max_inherent_impact');
            $table->string('description_inherent_impact');
            $table->integer('max_inherent_frequency');
            $table->string('description_inherent_frequency');
            $table->integer('inherent_exposition');
            $table->string('controls_decrease');
            $table->string('nature');
            $table->string('evidence');
            $table->string('coverage');
            $table->string('documentation');
            $table->string('segregation');
            $table->string('control_evaluation');
            $table->string('percentege_mitigation');
            $table->integer('max_residual_impact');
            $table->string('description_residual_impact');
            $table->integer('max_residual_frequency');
            $table->string('description_residual_frequency');
            $table->integer('residual_exposition');
            $table->text('max_impact_event_risk');


            $table->foreign('rm_subprocess_id')->references('id')->on('sau_rm_risk_matrix_subprocess')->onDelete('cascade');
            $table->foreign('risk_id')->references('id')->on('sau_rm_risk')->onDelete('cascade');

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
        Schema::dropIfExists('sau_rm_subprocess_risk');
    }
}
