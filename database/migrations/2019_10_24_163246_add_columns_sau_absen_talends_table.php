<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauAbsenTalendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_absen_talends', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('state')->after('company_id')->default('NO');
            $table->renameColumn('route', 'path');
            $table->string('file')->nullable()->change();
            $table->string('file_original_name')->after('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_absen_talends', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('state');
            $table->renameColumn('path', 'route');
            $table->dropColumn('file_original_name');
        });
    }
}
