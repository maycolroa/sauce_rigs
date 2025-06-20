<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearClonTablasContratistas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_user_information_contract_lessee_multilogin', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('information_id');
            
            $table->foreign('user_id', 'user_contract_multilogin_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('information_id', 'contract_multilogin_id')->references('id')->on('sau_ct_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_user_information_contract_lessee_multilogin');
    }
}
