<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeRolSauAwPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_aw_form_accidents_people', function (Blueprint $table) {
            $table->string('type_rol')->after('rol')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_aw_form_accidents_people', function (Blueprint $table) {
            $table->dropColumn('type_rol');
        });
    }
}
