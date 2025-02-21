<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAccidentsInfoSauCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_companies', function (Blueprint $table) {
            $table->string('nombre_actividad_economica_sede_principal')->nullable();
            $table->string('tipo_identificacion_sede_principal')->nullable();
            $table->string('identificacion_sede_principal')->nullable();
            $table->string('direccion_sede_principal')->nullable();
            $table->string('telefono_sede_principal')->nullable();
            $table->string('email_sede_principal')->nullable();
            $table->integer('departamento_sede_principal_id')->nullable();
            $table->integer('ciudad_sede_principal_id')->nullable();
            $table->string('zona_sede_principal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_companies', function (Blueprint $table) {
            $table->dropColumn('nombre_actividad_economica_sede_principal');
            $table->dropColumn('tipo_identificacion_sede_principal');
            $table->dropColumn('identificacion_sede_principal');
            $table->dropColumn('direccion_sede_principal');
            $table->dropColumn('telefono_sede_principal');
            $table->dropColumn('email_sede_principal');
            $table->dropColumn('departamento_sede_principal_id');
            $table->dropColumn('ciudad_sede_principal_id');
            $table->dropColumn('zona_sede_principal');
        });
    }
}
