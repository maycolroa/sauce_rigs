<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeQualificationControversySauCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('type_controversy_origin_1', 100)->nullable()->after('emitter_controversy_origin_1');
            $table->string('type_controversy_origin_2', 100)->nullable()->after('emitter_controversy_origin_2');
            $table->double('punctuation_controversy_plc_1', 10, 2)->default(0)->nullable()->after('emitter_controversy_pcl_1');
            $table->double('punctuation_controversy_plc_2', 10, 2)->default(0)->nullable()->after('emitter_controversy_pcl_2');
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
            $table->dropColumn('type_controversy_origin_1');
            $table->dropColumn('type_controversy_origin_2');
            $table->dropColumn('punctuation_controversy_plc_1');
            $table->dropColumn('punctuation_controversy_plc_2');
        });
    }
}
