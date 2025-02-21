<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnConditionsCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_companies', function ($table) {
            $table->boolean('ph_state_incentives')->default(false);
            $table->string('ph_file_incentives')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_companies', function (Blueprint $table) {
            $table->dropColumn('ph_state_incentives');
            $table->dropColumn('ph_file_incentives');
        });
    }
}
