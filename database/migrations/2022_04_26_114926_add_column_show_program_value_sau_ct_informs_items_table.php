<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShowProgramValueSauCtInformsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_inform_theme_item', function (Blueprint $table) {
            $table->string('show_program_value')->default('SI')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_inform_theme_item', function (Blueprint $table) {
            $table->dropColumn('show_program_value');
        });
    }
}
