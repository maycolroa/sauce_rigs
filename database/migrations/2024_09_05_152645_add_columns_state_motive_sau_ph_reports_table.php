<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsStateMotiveSauPhReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_reports', function (Blueprint $table) {            
            $table->string('state')->nullable();
            $table->text('motive')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_reports', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('motive');
        });
    }
}
