<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeLicenseSauReDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rs_drivers', function (Blueprint $table) {            
            $table->dropColumn('type_license');  
            $table->unsignedInteger('type_license_id')->after('employee_id')->nullable();
            $table->foreign('type_license_id')->references('id')->on('sau_rs_tag_type_license')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rs_drivers', function (Blueprint $table) { 
            $table->dropColumn('type_license_id');  
            $table->string('type_license')->nullable();    
        });
    }
}
