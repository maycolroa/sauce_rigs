<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingSauDmQualificationValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_values', function (Blueprint $table) {
            $table->foreign('qualification_type_id')->references('id')->on('sau_dm_qualification_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_qualification_values', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_values_qualification_type_id_foreign');
        });
    }
}
