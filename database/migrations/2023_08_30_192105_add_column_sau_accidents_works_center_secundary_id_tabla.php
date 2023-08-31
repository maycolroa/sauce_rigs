<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauAccidentsWorksCenterSecundaryIdTabla extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_form_accidents', function (Blueprint $table) {
            $table->integer('centro_trabajo_secundary_id')->nullable()->after('info_sede_principal_misma_centro_trabajo');
            $table->string('estado_evento')->default('Reportado');
            $table->string('investigation_arl')->default('NO');
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
            $table->dropColumn('centro_trabajo_secundary_id');
            $table->dropColumn('estado_evento');
            $table->dropColumn('investigation_arl');
        });
    }
}
