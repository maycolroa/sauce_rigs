<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTableSauDmQualificationMethodologies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('sau_dm_qualification_methodologies', 'sau_dm_qualification_danger');
        Schema::table('sau_dm_qualification_danger', function (Blueprint $table) {
            $table->renameColumn('type', 'type_id');
            $table->renameColumn('qualification', 'value_id');
        });
        Schema::table('sau_dm_qualification_danger', function (Blueprint $table) {
            $table->integer('type_id')->change();
            $table->integer('value_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('sau_dm_qualification_danger', 'sau_dm_qualification_methodologies');
        Schema::table('sau_dm_qualification_methodologies', function (Blueprint $table) {
            $table->renameColumn('type_id', 'type');
            $table->renameColumn('value_id', 'qualification');
        });
        Schema::table('sau_dm_qualification_methodologies', function (Blueprint $table) {
            $table->string('type')->change();
            $table->string('qualification')->change();
        });
        
    }
}
