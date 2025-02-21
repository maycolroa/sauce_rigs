<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SauEppWastes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_epp_wastes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('element_id');
            $table->string('code_element');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('employee_id');

            $table->foreign('element_id')->references('id')->on('sau_epp_elements_balance_specific')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('sau_epp_locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('sau_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_epp_wastes');
    }
}
