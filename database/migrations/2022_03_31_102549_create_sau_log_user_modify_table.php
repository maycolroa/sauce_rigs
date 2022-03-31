<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauLogUserModifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_log_user_modify', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('modifier_user');
            $table->unsignedInteger('modified_user');
            $table->string('modification');
            $table->string('roles_old')->nullable();
            $table->string('roles_new')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('modifier_user')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('modified_user')->references('id')->on('sau_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_log_user_modify');
    }
}
