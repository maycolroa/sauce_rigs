<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsNewsSauCtContractEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_contract_employees', function (Blueprint $table) {            
            $table->string('direction')->nullable();
            $table->string('sex')->nullable();
            $table->string('phone_residence')->nullable();
            $table->string('phone_movil')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedInteger('employee_eps_id')->nullable();
            $table->string('disability_condition')->nullable();
            $table->string('rh')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->float('salary')->nullable();

            $table->foreign('employee_eps_id')->references('id')->on('sau_employees_eps')->onUpdate('cascade')->onDelete('cascade'); 
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
            $table->dropColumn('direction');  
            $table->dropColumn('sex');  
            $table->dropColumn('phone_residence');  
            $table->dropColumn('phone_movil');  
            $table->dropColumn('date_of_birth');  
            $table->dropColumn('employee_eps_id');  
            $table->dropColumn('disability_condition');  
            $table->dropColumn('rh');  
            $table->dropColumn('emergency_contact');  
            $table->dropColumn('salary');  
        });
    }
}
