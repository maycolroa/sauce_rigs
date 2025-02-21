<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSauBmMusculoskeletalAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_bm_musculoskeletal_analysis', function (Blueprint $table) {
            $table->dropColumn('document_type');
            $table->dropColumn('professional_identification');
            $table->dropColumn('professional');
            $table->dropColumn('order');
            $table->dropColumn('attention_code');
            $table->dropColumn('attention');
            $table->dropColumn('department');
            $table->dropColumn('nit_company');
            $table->dropColumn('nit_company_mission');
            $table->dropColumn('company_mission');
            $table->dropColumn('etareo_group');
            $table->dropColumn('stratum');
            $table->dropColumn('number_people_charge');
            $table->dropColumn('scholarship');
            $table->dropColumn('marital_status');

            $table->string('state', 15)->after('antiquity')->nullable();

            $table->dropColumn('pressure_systolic');
            $table->dropColumn('pressure_diastolic');
            $table->dropColumn('diagnostic_code_14');
            $table->dropColumn('diagnostic_14');
            $table->dropColumn('diagnostic_code_15');
            $table->dropColumn('diagnostic_15');
            $table->dropColumn('diagnostic_code_16');
            $table->dropColumn('diagnostic_16');
            $table->dropColumn('diagnostic_code_17');
            $table->dropColumn('diagnostic_17');
            $table->dropColumn('diagnostic_code_18');
            $table->dropColumn('diagnostic_18');
            $table->dropColumn('authorization_access_information');
            $table->dropColumn('date_end');
            $table->dropColumn('optometry');
            $table->dropColumn('visiometry');
            $table->dropColumn('audiometry');
            $table->dropColumn('spirometry');
            $table->dropColumn('tracing');

            $table->text('id3')->after('symptomatology_observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_bm_musculoskeletal_analysis', function (Blueprint $table) {
        });
    }
}
