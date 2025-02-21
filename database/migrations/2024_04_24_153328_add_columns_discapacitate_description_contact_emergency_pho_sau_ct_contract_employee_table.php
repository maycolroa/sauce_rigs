<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDiscapacitateDescriptionContactEmergencyPhoSauCtContractEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {            
            $table->text('disability_description')->nullable();
            $table->string('emergency_contact_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {            
            $table->dropColumn('disability_description');
            $table->dropColumn('emergency_contact_phone');
        });
    }
}
