<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsNewClassEquipoManualesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {
            $table->string('data_sheet')->nullable();
            $table->string('user_manual')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {   
            $table->dropColumn('data_sheet');  
            $table->dropColumn('user_manual');        
        });
    }
}
