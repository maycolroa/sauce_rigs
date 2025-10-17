<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCodeNitSauEmployeesAfpTale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('sau_employees_afp', function (Blueprint $table) {   
            $table->string('code_nit')->nullable()->after('code');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('sau_employees_afp', function (Blueprint $table) {            
            $table->dropColumn('code_nit');      
        });*/
    }
}
