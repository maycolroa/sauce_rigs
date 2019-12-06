<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnQualificationOriginSauReincCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('type_qualification_origin', 100)->nullable()->after('emitter_origin');            
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
            $table->dropColumn('type_qualification_origin');
        });
    }
}
