<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAwFormAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_form_accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('employee_id')->nullable();

            $table->string('tipo_vinculacion_persona');
            $table->string('nombre_persona')->nullable();
            $table->string('tipo_identificacion_persona')->nullable();
            $table->string('identificacion_persona')->nullable();
            $table->date('fecha_nacimiento_persona')->nullable();
            $table->string('sexo_persona')->nullable();
            $table->string('direccion_persona')->nullable();
            $table->string('telefono_persona')->nullable();
            $table->string('email_persona')->nullable();
            $table->string('departamento_persona_id');
            $table->string('ciudad_persona_id');
            $table->string('zona_persona');
            $table->string('cargo_persona')->nullable();
            $table->unsignedInteger('employee_position_id')->nullable();
            $table->string('tiempo_ocupacion_habitual_persona');
            $table->date('fecha_ingreso_empresa_persona')->nullable();
            $table->integer('salario_persona');
            $table->string('jornada_trabajo_habitual_persona');

            $table->string('tipo_vinculador_laboral');
            $table->string('razon_social');
            $table->string('nombre_actividad_economica_sede_principal');
            $table->string('tipo_identificacion_sede_principal');
            $table->string('identificacion_sede_principal');
            $table->string('direccion_sede_principal');
            $table->string('telefono_sede_principal');
            $table->string('email_sede_principal');
            $table->string('departamento_sede_principal_id');
            $table->string('ciudad_sede_principal_id');
            $table->string('zona_sede_principal');

            $table->boolean('info_sede_principal_misma_centro_trabajo')->default(1);
            $table->string('nombre_actividad_economica_centro_trabajo')->nullable();
            $table->string('direccion_centro_trabajo')->nullable();
            $table->string('telefono_centro_trabajo')->nullable();
            $table->string('email_centro_trabajo')->nullable();
            $table->string('departamento_centro_trabajo_id')->nullable();
            $table->string('ciudad_centro_trabajo_id')->nullable();
            $table->string('zona_centro_trabajo')->nullable();

            $table->string('nivel_accidente');
            $table->date('fecha_envio_arl')->nullable();
            $table->date('fecha_envio_empresa')->nullable();
            $table->string('coordinador_delegado');
            $table->string('cargo');
            $table->unsignedInteger('employee_eps_id')->nullable();
            $table->unsignedInteger('employee_arl_id')->nullable();
            $table->unsignedInteger('employee_afp_id')->nullable();
            $table->boolean('tiene_seguro_social')->default(0);
            $table->string('nombre_seguro_social')->nullable();

            $table->dateTime('fecha_accidente')->nullable();
            $table->string('jornada_accidente');
            $table->boolean('estaba_realizando_labor_habitual')->default(1);
            $table->string('otra_labor_habitual')->nullable();
            $table->string('total_tiempo_laborado');
            $table->string('tipo_accidente');
            $table->string('departamento_accidente');
            $table->string('ciudad_accidente');
            $table->string('zona_accidente');
            $table->string('accidente_ocurrio_dentro_empresa');
            $table->boolean('causo_muerte')->default(0);
            $table->date('fecha_muerte')->nullable();
            $table->string('otro_sitio')->nullable();
            $table->string('otro_mecanismo')->nullable();
            $table->string('otra_lesion')->nullable();

            $table->text('descripcion_accidente');
            $table->boolean('personas_presenciaron_accidente')->default(0);
            $table->string('nombres_apellidos_responsable_informe');
            $table->string('cargo_responsable_informe');
            $table->string('tipo_identificacion_responsable_informe');
            $table->string('identificacion_responsable_informe');
            $table->date('fecha_diligenciamiento_informe')->nullable();

            $table->text('observaciones_empresa');

            $table->boolean('consolidado')->default(0);

            $table->unsignedInteger('user_id')->unsigned();
            $table->unsignedInteger('site_id')->unsigned()->nullable();
            $table->unsignedInteger('agent_id')->unsigned()->nullable();
            $table->unsignedInteger('mechanism_id')->unsigned()->nullable();

            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('employee_id')->references('id')->on('sau_employees');
            $table->foreign('site_id')->references('id')->on('sau_aw_sites');
            $table->foreign('agent_id')->references('id')->on('sau_aw_agents');
            $table->foreign('mechanism_id')->references('id')->on('sau_aw_mechanisms');
            $table->foreign('employee_position_id')->references('id')->on('sau_employees_positions');
            $table->foreign('employee_eps_id')->references('id')->on('sau_employees_eps');
            $table->foreign('employee_afp_id')->references('id')->on('sau_employees_afp');
            $table->foreign('employee_arl_id')->references('id')->on('sau_employees_arl');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_aw_form_accidents');
    }
}
