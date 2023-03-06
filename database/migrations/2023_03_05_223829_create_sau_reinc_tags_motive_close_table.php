<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauReincTagsMotiveCloseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_tags_motive_close', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            
            $table->timestamps();
        });

        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->text('motive_close')->after('deadline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_reinc_tags_motive_close');

        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->dropColumn('motive_close');
        });
    }
}
