<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtItemQualificationContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_item_qualification_contract', function (Blueprint $table) {
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('qualification_id');
            $table->unsignedInteger('contract_id');

            $table->foreign('item_id')->references('id')->on('sau_ct_section_category_items')->onDelete('cascade');
            $table->foreign('qualification_id')->references('id')->on('sau_ct_qualifications')->onDelete('cascade');
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
        Schema::dropIfExists('sau_ct_item_qualification_contract');
    }
}
