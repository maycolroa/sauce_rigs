<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsNullableAddSauAwFormAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_form_accidents', function (Blueprint $table) {
            $table->unsignedInteger('type_lesion_id')->nullable();
            $table->unsignedInteger('parts_body_id')->nullable();
            $table->string('otro_agente')->nullable();
            $table->string('otra_parte')->nullable();
            $table->string('tipo_vinculacion_persona')->nullable()->change();
            $table->string('razon_social')->nullable()->change();
            $table->string('tipo_identificacion_sede_principal')->nullable()->change();
            $table->string('identificacion_sede_principal')->nullable()->change();
            $table->string('direccion_sede_principal')->nullable()->change();
            $table->string('telefono_sede_principal')->nullable()->change();
            $table->string('email_sede_principal')->nullable()->change();
            $table->integer('departamento_sede_principal_id')->nullable()->change();
            $table->integer('ciudad_sede_principal_id')->nullable()->change();
            $table->string('zona_sede_principal')->nullable()->change();
            $table->boolean('causo_muerte')->nullable()->change();


            $table->foreign('type_lesion_id')->references('id')->on('sau_aw_types_lesion')->onDelete('cascade');
            $table->foreign('parts_body_id')->references('id')->on('sau_aw_parts_body')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_aw_form_accidents', function (Blueprint $table) {
            $table->dropColumn('type_lesion_id');
            $table->dropColumn('parts_body_id');
            $table->dropColumn('otro_agente');
            $table->dropColumn('otra_parte');
            $table->string('tipo_vinculacion_persona')->change();
            $table->boolean('causo_muerte')->default(0)->change();
            $table->string('razon_social')->change();
            $table->string('tipo_identificacion_sede_principal')->change();
            $table->string('identificacion_sede_principal')->change();
            $table->string('direccion_sede_principal')->change();
            $table->string('telefono_sede_principal')->change();
            $table->string('email_sede_principal')->change();
            $table->integer('departamento_sede_principal_id')->change();
            $table->integer('ciudad_sede_principal_id')->change();
            $table->string('zona_sede_principal')->change();
        });

    }
}
