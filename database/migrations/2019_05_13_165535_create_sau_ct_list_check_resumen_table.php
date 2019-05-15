<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtListCheckResumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_list_check_resumen', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id');
            $table->integer('total_standard')->default(0);
            $table->integer('total_c')->default(0);
            $table->integer('total_nc')->default(0);
            $table->integer('total_sc')->default(0);
            $table->double('total_p_c')->default(0);
            $table->double('total_p_nc')->default(0);
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_list_check_resumen');
    }
}
