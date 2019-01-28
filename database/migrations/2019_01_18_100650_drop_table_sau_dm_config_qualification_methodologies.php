<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableSauDmConfigQualificationMethodologies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_config_qualification_methodologies', function (Blueprint $table) {
            $table->dropForeign('sau_dm_config_qualification_methodologies_company_id_foreign');
        });
        
        Schema::dropIfExists('sau_dm_config_qualification_methodologies');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sau_dm_config_qualification_methodologies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->longText('types');
            $table->longText('qualifications');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies');
        });
    }
}
