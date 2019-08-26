<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingSauDmQualificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_types', function (Blueprint $table) {
            $table->foreign('qualification_id')->references('id')->on('sau_dm_qualifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_dm_qualification_types', function (Blueprint $table) {
            $table->dropForeign('sau_dm_qualification_types_qualification_id_foreign');
        });
    }
}
