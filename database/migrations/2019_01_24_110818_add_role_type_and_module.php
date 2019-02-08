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
            $table->string('type_role', 20)->after('company_id')->default('estatico');
            $table->unsignedInteger('module_id')->after('type_role')->nullable();
            
            $table->foreign('module_id')->references('id')->on('sau_modules');
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
            $table->dropForeign('sau_roles_module_id_foreign');
            $table->dropColumn('type_role');
            $table->dropColumn('module_id');
        });
    }
}
