<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleTypeAndModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_roles', function (Blueprint $table) {
            $table->string('type_role', 50)->after('company_id');
            $table->string('module', 50)->after('type_role')->nullable()->references('id')->on('sau_modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_roles', function (Blueprint $table) {
            $table->dropColumn('type_role');
            $table->dropColumn('module');
        });
    }
}
