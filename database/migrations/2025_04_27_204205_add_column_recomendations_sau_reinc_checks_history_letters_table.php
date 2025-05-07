<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRecomendationsSauReincChecksHistoryLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_letter_recommendations_history', function (Blueprint $table) {   
            $table->text('detail')->nullable();
            $table->date('start_recommendations')->nullable();
            $table->date('end_recommendations')->nullable();
            $table->string('indefinite_recommendations')->nullable();
            $table->string('origin_recommendations')->nullable();
            $table->string('disease_origin')->nullable();
            $table->string('Observations_recommendatios')->nullable();
            $table->string('position_functions_assigned_reassigned')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_letter_recommendations_history', function (Blueprint $table) {            
            $table->dropColumn('detail');         
            $table->dropColumn('start_recommendations');         
            $table->dropColumn('end_recommendations');         
            $table->dropColumn('indefinite_recommendations');
            $table->dropColumn('origin_recommendations');      
            $table->dropColumn('disease_origin');    
            $table->dropColumn('Observations_recommendatios');    
            $table->dropColumn('position_functions_assigned_reassigned');    
        });
    }
}
