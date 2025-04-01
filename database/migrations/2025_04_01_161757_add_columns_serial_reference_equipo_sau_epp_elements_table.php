<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSerialReferenceEquipoSauEppElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {   
            $table->string('serial')->nullable()->after('expiration_date');
            $table->string('reference')->nullable()->after('serial');
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
            $table->dropColumn('serial');      
            $table->dropColumn('reference');
        });
    }
}
