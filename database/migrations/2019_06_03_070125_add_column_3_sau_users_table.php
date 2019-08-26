<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumn3SauUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_users', function (Blueprint $table) {
            $table->string('default_module_url')->nullable()->after('remember_token');
            $table->unsignedInteger('module_id')->nullable()->after('default_module_url');

            $table->foreign('module_id')->references('id')->on('sau_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_users', function (Blueprint $table) {
            $table->dropForeign('sau_users_module_id_foreign');
            $table->dropColumn('default_module_url');
            $table->dropColumn('module_id');

        });
    }
}
