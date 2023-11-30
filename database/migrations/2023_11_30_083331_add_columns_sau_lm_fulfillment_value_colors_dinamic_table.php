<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauLmFulfillmentValueColorsDinamicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_qualifications_color_dinamic', function (Blueprint $table) {
            $table->string('no_vigente')->nullable()->after('informativo');
            $table->string('en_transicion')->nullable()->after('no_vigente');
            $table->string('pendiente_reglamentacion')->nullable()->after('en_transicion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_qualifications_color_dinamic', function (Blueprint $table) {   
            $table->dropColumn('no_vigente');  
            $table->dropColumn('en_transicion');  
            $table->dropColumn('pendiente_reglamentacion');        
        });
    }
}
