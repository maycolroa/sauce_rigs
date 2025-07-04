<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCtItemsQualificationInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_item_qualification_lessee', function (Blueprint $table) {
            $table->unsignedInteger('list_check_item_id');
            $table->unsignedInteger('qualification_id');
            $table->unsignedInteger('information_id');
                    
            $table->foreign('list_check_item_id')->references('id')->on('sau_ct_list_check_items')->onDelete('cascade');
            $table->foreign('qualification_id')->references('id')->on('sau_ct_qualifications')->onDelete('cascade');
            $table->foreign('information_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_item_qualification');
    }
}
