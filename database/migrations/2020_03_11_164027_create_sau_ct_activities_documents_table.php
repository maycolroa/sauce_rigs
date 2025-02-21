<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtActivitiesDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_activities_documents', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('activity_id');
            $table->text('name');
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('sau_ct_activities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_activities_documents');
    }
}
