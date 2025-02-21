<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauReincReportsChangeFunctionsObservationsRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('has_function_setting')->nullable();
            $table->text('function_setting')->nullable();
            $table->text('Observations_recommendatios')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->dropColumn('has_function_setting');
            $table->dropColumn('function_setting');
            $table->dropColumn('Observations_recommendatios');
        });
    }
}
