<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDetailsDiseaseOriginSauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('disease_origin_recomendations')->nullable();
            $table->string('disease_origin_recomendations_2')->nullable();
            $table->string('disease_origin_recomendations_3')->nullable();
            $table->string('disease_origin_recomendations_4')->nullable();
            $table->string('disease_origin_recomendations_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {   
            $table->dropColumn('disease_origin_recomendations');  
            $table->dropColumn('disease_origin_recomendations_2');  
            $table->dropColumn('disease_origin_recomendations_3');  
            $table->dropColumn('disease_origin_recomendations_4');  
            $table->dropColumn('disease_origin_recomendations_5');  
        });
    }
}
