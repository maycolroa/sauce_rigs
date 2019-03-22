<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableSauConfLocationLevelForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_conf_location_level_forms', function (Blueprint $table) {
            $table->dropForeign('sau_conf_location_level_forms_company_id_foreign');
            $table->dropForeign('sau_conf_location_level_forms_module_id_foreign');
        });
        
        Schema::dropIfExists('sau_conf_location_level_forms');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sau_conf_location_level_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('module_id');
            $table->string('regional');
            $table->string('headquarter');
            $table->string('area');
            $table->string('process');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies');
            $table->foreign('module_id')->references('id')->on('sau_modules');
        });
    }
}
