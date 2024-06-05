<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStateSauEmployeeEpsAndArlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_employees_eps', function (Blueprint $table) {
            $table->boolean('state')->default(true);
        });

        Schema::table('sau_employees_arl', function (Blueprint $table) {
            $table->boolean('state')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_employees_eps', function (Blueprint $table) {
            $table->dropColumn('state');
        });

        Schema::table('sau_employees_arl', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
