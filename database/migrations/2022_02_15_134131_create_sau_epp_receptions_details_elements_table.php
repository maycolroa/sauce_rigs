<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauEppReceptionsDetailsElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_receptions_details_elements', function (Blueprint $table) {
            $table->unsignedInteger('reception_detail_id');
            $table->unsignedInteger('element_specific_id');
            
            $table->foreign('reception_detail_id','reception_detail_id_foreign')->references('id')->on('sau_epp_receptions_details')->onDelete('cascade');
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
        Schema::dropIfExists('sau_epp_receptions_details_elements');
    }
}
