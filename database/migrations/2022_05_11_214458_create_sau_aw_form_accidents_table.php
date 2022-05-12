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

            $table->string('nivel_accidente');
            $table->date('fecha_envio_arl')->nullable();
            $table->date('fecha_envio_empresa')->nullable();
            $table->string('coordinador_delegado');
            $table->string('cargo');
            $table->string('employee_eps_id')->nullable();
            $table->string('codigo_arl')->nullable();
            $table->string('codigo_afp')->nullable();
            $table->boolean('tiene_seguro_social')->default(0);
            $table->string('nombre_seguro_social')->nullable();

            $table->string('tipo_vinculador_laboral');
            $table->string('razon_social');
            $table->string('nombre_actividad_economica_sede_principal');
            $table->string('tipo_identificacion_sede_principal');
            $table->string('identificacion_sede_principal');
            $table->string('direccion_sede_principal');
            $table->string('telefono_sede_principal');
            $table->string('fax_sede_principal');
            $table->string('email_sede_principal');
            $table->string('departamento_sede_principal_id');
            $table->string('ciudad_sede_principal_id');
            $table->string('zona_sede_principal');

            $table->boolean('info_sede_principal_misma_centro_trabajo')->default(1);
            $table->string('nombre_actividad_economica_centro_trabajo');
            $table->string('direccion_centro_trabajo');
            $table->string('telefono_centro_trabajo');
            $table->string('fax_centro_trabajo');
            $table->string('email_centro_trabajo');
            $table->string('departamento_centro_trabajo_id');
            $table->string('ciudad_centro_trabajo_id');
            $table->string('zona_centro_trabajo');

            $table->string('tipo_vinculacion_persona');
            $table->string('primer_apellido_persona');
            $table->string('segundo_apellido_persona')->nullable();
            $table->string('primer_nombre_persona');
            $table->string('segundo_nombre_persona')->nullable();
            $table->string('tipo_identificacion_persona');
            $table->string('identificacion_persona');
            $table->date('fecha_nacimiento_persona')->nullable();
            $table->string('sexo_persona');
            $table->string('direccion_persona')->nullable();
            $table->string('telefono_persona')->nullable();
            $table->string('fax_persona')->nullable();
            $table->string('email_persona')->nullable();
            $table->string('departamento_persona_id');
            $table->string('ciudad_persona_id');
            $table->string('zona_persona');
            $table->string('employee_position_id');
            $table->string('tiempo_ocupacion_habitual_persona');
            $table->date('fecha_ingreso_empresa_persona')->nullable();
            $table->integer('salario_persona');
            $table->string('jornada_trabajo_habitual_persona');

            $table->dateTime('fecha_accidente')->nullable();
            $table->string('jornada_accidente');
            $table->boolean('estaba_realizando_labor_habitual')->default(1);
            $table->string('otra_labor_habitual');
            $table->string('total_tiempo_laborado');
            $table->string('tipo_accidente');
            $table->string('departamento_accidente');
            $table->string('ciudad_accidente');
            $table->string('zona_accidente');
            $table->boolean('accidente_ocurrio_dentro_empresa');
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

            $table->integer('user_id')->unsigned();
            $table->integer('site_id')->unsigned()->nullable();
            $table->integer('agent_id')->unsigned()->nullable();
            $table->integer('mechanism_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('sau_users');
            $table->foreign('site_id')->references('id')->on('sau_aw_sites');
            $table->foreign('agent_id')->references('id')->on('sau_aw_agents');
            $table->foreign('mechanism_id')->references('id')->on('sau_aw_mechanisms');
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
