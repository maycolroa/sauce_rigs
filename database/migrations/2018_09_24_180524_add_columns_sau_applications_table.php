<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_applications', function (Blueprint $table) {
            $table->string('display_name')->after('name');
            $table->string('image')->after('display_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_applications', function (Blueprint $table) {
            $table->dropColumn('display_name');
            $table->dropColumn('image');
        });
    }
}
