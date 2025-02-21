<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmSauReincCheckTypeClasificationOriginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('qualification_controversy_1', 100)->nullable()->after('emitter_controversy_origin_1');
            $table->string('is_firm_controversy_1', 100)->nullable()->after('qualification_controversy_1');
            $table->string('qualification_controversy_2', 100)->nullable()->after('emitter_controversy_origin_2');
            $table->string('is_firm_controversy_pcl_1', 100)->nullable()->after('punctuation_controversy_plc_1');
            
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
            $table->dropColumn('qualification_controversy_1');
            $table->dropColumn('is_firm_controversy_1');
            $table->dropColumn('qualification_controversy_2');
            $table->dropColumn('is_firm_controversy_pcl_1');
        });
    }
}
