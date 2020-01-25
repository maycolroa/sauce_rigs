<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmSauReincChecksSubsystemConclusionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('subsystem')->nullable()->after('cie10_code_id');
            $table->string('conclusion_recommendations')->nullable()->after('origin_recommendations');                 
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
            $table->dropColumn('subsystem');
            $table->dropColumn('conclusion_recommendations');
        });
    }
}
