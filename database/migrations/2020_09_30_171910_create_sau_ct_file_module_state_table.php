<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtFileModuleStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_file_module_state', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('contract_id');            
            $table->unsignedInteger('file_id');
            $table->string('module');
            $table->string('state');
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('sau_ct_information_contract_lessee')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('sau_ct_file_upload_contracts_leesse')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_file_module_state');
    }
}
