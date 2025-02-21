<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmComplementaryRiskAssessmentMethodologiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_complementary_methodologies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->string('file');
            $table->string('type');
            $table->text('observations');
            $table->unsignedInteger('user_id');
            $table->timestamps();


            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id', 'dm_methodology_user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_dm_complementary_methodology_log_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('methodology_id');
            $table->string('name_old');
            $table->string('name_new');
            $table->string('type_old');
            $table->string('type_new');
            $table->text('observations_old');
            $table->text('observations_new');
            $table->unsignedInteger('user_id');
            $table->timestamps();


            $table->foreign('user_id', 'dm_methodology_history_user_id')->references('id')->on('sau_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('methodology_id', 'dm_methodology_history_methodology_id')->references('id')->on('sau_dm_complementary_methodologies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_complementary_methodology_log_histories');
        Schema::dropIfExists('sau_dm_complementary_methodologies');
    }
}
