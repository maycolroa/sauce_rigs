<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEmailTrainingEmployeeSauCtInformationContractLesseeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_information_contract_lessee', function ($table) {
            $table->string('email_training_employee')->nullable()->after('social_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_information_contract_lessee', function ($table) {
            $table->dropColumn('email_training_employee');
        });
    }
}
