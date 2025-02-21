<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppIncomeDetailElementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_income_detail_element', function (Blueprint $table) {
            $table->unsignedInteger('income_detail_id');
            $table->unsignedInteger('element_specific_id');
            
            $table->foreign('income_detail_id','income_detail_id_foreign')->references('id')->on('sau_epp_incomen_details')->onDelete('cascade');
            $table->foreign('element_specific_id')->references('id')->on('sau_epp_elements_balance_specific')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_income_detail_element');
    }
}
