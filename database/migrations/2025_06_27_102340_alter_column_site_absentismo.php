<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnSiteAbsentismo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_absen_reports', function (Blueprint $table) {   
            $table->string('name_show')->nullable()->change();
            $table->string('name_report')->nullable()->change();
            $table->string('user')->nullable()->change();
            $table->string('site', 300)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
