<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_permissions', function (Blueprint $table) {
            $table->integer('module_id')->after('description')->unsigned();
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
        Schema::table('sau_permissions', function (Blueprint $table) {
          $table->dropForeign('sau_permissions_module_id_foreign');
            $table->dropColumn('module_id');
        });
    }
}
