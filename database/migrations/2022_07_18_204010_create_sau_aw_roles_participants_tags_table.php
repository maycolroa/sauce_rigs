<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauAwRolesParticipantsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_aw_roles_participants_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('system');
            $table->unsignedInteger('company_id')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('sau_aw_roles_participants_tags');
    }
}
