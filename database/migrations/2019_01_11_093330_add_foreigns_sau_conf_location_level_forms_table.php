<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignsSauConfLocationLevelFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_conf_location_level_forms', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('sau_companies');
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
        Schema::table('sau_conf_location_level_forms', function (Blueprint $table) {
            $table->dropForeign('sau_conf_location_level_forms_company_id_foreign');
            $table->dropForeign('sau_conf_location_level_forms_module_id_foreign');
        });
    }
}
