<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauDmHistoryQualificationChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_dm_history_qualification_change', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('danger_matrix_id');
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('danger_id');
            $table->unsignedInteger('activity_danger_id');
            $table->text('qualification_old');
            $table->text('qualification_new');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('danger_matrix_id')->references('id')->on('sau_dangers_matrix')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('sau_dm_activities')->onDelete('cascade');
            $table->foreign('danger_id')->references('id')->on('sau_dm_dangers')->onDelete('cascade');
            $table->foreign('activity_danger_id')->references('id')->on('sau_dm_activity_danger')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_dm_history_qualification_change');
    }
}
