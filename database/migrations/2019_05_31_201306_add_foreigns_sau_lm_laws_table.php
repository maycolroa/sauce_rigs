<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauLmLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->foreign('law_type_id')->references('id')->on('sau_lm_laws_types')->onDelete('cascade');
            $table->foreign('risk_aspect_id')->references('id')->on('sau_lm_risks_aspects')->onDelete('cascade');
            $table->foreign('entity_id')->references('id')->on('sau_lm_entities')->onDelete('cascade');
            $table->foreign('sst_risk_id')->references('id')->on('sau_lm_sst_risks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->dropForeign('sau_lm_laws_law_type_id_foreign');
            $table->dropForeign('sau_lm_laws_risk_aspect_id_foreign');
            $table->dropForeign('sau_lm_laws_entity_id_foreign');
            $table->dropForeign('sau_lm_laws_sst_risk_id_foreign');
        });
    }
}
