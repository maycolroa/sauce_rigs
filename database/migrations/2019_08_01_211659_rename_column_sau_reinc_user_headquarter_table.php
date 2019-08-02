<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnSauReincUserHeadquarterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_reinc_user_headquarter', function (Blueprint $table) {
            $table->renameColumn('headquarter_id', 'employee_headquarter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_reinc_user_headquarter', function (Blueprint $table) {
            $table->renameColumn('employee_headquarter_id', 'headquarter_id');
        });
    }
}
