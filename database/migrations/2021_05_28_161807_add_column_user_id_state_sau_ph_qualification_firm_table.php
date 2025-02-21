<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserIdStateSauPhQualificationFirmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_qualification_inspection_firm', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('image')->nullable();
            $table->string('state')->after('user_id')->nullable();
            $table->unsignedInteger('company_id')->after('state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_qualification_inspection_firm', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('state');
            $table->dropColumn('company_id');
        });
    }
}
