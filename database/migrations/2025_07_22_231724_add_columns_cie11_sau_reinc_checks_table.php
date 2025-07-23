<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsCie11SauReincChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {               
            $table->unsignedInteger('cie11_code_id')->nullable()->after('cie10_code_id');
            $table->string('use_cie_10')->default('SI')->nullable();
            $table->string('update_cie_11')->default('NO')->nullable(); 
            $table->unsignedInteger('cie10_code_id')->nullable()->change();
            
            $table->foreign('cie11_code_id')->references('id')->on('sau_reinc_cie11_codes')->onDelete('cascade');
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
            $table->dropForeign('sau_reinc_checks_cie11_code_id_foreign');
            $table->dropColumn('cie11_code_id');         
            $table->dropColumn('use_cie_10');         
            $table->dropColumn('update_cie_11');  
        });
    }
}
