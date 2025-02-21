<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_users', function (Blueprint $table) {
            $table->string('medical_record')->nullable();
            $table->string('sst_license')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_users', function (Blueprint $table) {
            $table->dropColumn('medical_record');
            $table->dropColumn('sst_license');
        });
    }
}
