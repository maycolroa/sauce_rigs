<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsSauCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_roles', function (Blueprint $table) {
            $table->string('type_role')->default('No Definido')->change();
            $table->unsignedInteger('company_id')->nullable()->change();
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
            $table->string('type_role')->default('estatico')->change();
            $table->unsignedInteger('company_id')->nullable(false)->change();
        });
    }
}
