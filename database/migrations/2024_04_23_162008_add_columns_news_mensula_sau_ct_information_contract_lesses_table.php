<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsNewsMensulaSauCtInformationContractLessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) { 
            $table->string('social_security_payment_operator')->nullable();
            $table->string('ips')->nullable();
            $table->string('height_training_centers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_information_contract_lessee', function (Blueprint $table) {      
            $table->dropColumn('social_security_payment_operator');  
            $table->dropColumn('ips');  
            $table->dropColumn('height_training_centers');  
        });
    }
}
