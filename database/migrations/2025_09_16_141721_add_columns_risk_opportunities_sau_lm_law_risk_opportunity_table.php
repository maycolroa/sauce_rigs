<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRiskOpportunitiesSauLmLawRiskOpportunityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_law_risk_opportunity', function (Blueprint $table) {   
            $table->string('type_risk')->nullable();
            $table->string('risk_subsystem')->nullable();
            $table->string('risk_gestion')->nullable();
            $table->text('risk_id_text')->nullable();
            $table->text('description_no_apply')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_law_risk_opportunity', function (Blueprint $table) {          
            $table->dropColumn('type_risk');    
            $table->dropColumn('risk_subsystem');    
            $table->dropColumn('risk_gestion');     
            $table->dropColumn('risk_id_text');      
            $table->dropColumn('description_no_apply');  
        });
    }
}
