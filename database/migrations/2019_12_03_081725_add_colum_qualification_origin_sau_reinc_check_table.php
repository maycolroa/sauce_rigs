<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumQualificationOriginSauReincCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('qualification_origin')->nullable()->after('emitter_origin');
            $table->string('is_firm_process_origin')->nullable()->after('qualification_origin'); 
            $table->string('is_firm_process_pcl')->nullable()->after('entity_rating_pcl'); 
                 
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
            $table->dropColumn('qualification_origin');
            $table->dropColumn('is_firm_process_origin');
            $table->dropColumn('is_firm_process_pcl');
        });
    }
}
