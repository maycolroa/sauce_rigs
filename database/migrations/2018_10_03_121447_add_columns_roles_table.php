<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_roles', function (Blueprint $table) {
            $table->integer('company_id')->after('description')->unsigned();
            $table->foreign('company_id')->references('id')->on('sau_companies');
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
          $table->dropForeign('sau_roles_company_id_foreign');
          $table->dropColumn('company_id');
        });
    }
}
