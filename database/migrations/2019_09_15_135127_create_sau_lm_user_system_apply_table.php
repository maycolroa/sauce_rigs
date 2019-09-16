<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLmUserSystemApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_lm_user_system_apply', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("system_apply_id");
            $table->unsignedInteger('company_id');

            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('system_apply_id')->references('id')->on('sau_lm_system_apply')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_lm_user_system_apply');
    }
}
