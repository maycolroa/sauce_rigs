<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsNewSauCtContractsEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {            
            $table->text('workday')->nullable();
            $table->string('civil_status')->nullable();
            $table->boolean('state_employee')->default(true);
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
            $table->dropColumn('workday');
            $table->dropColumn('civil_status');
            $table->dropColumn('state_employee');
        });
    }
}
