<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCollumnSauLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->renameColumn('apply_system', 'system_apply_id');
            $table->unsignedInteger('company_id')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->renameColumn('system_apply_id', 'apply_system');
            $table->dropColumn('company_id');
        });
    }
}
