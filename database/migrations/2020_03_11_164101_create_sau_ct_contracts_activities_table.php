<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtContractsActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_contracts_activities', function (Blueprint $table) {
            $table->unsignedInteger("contract_id");
            $table->unsignedInteger("activity_id");

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('sau_ct_activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_contracts_activities');
    }
}
