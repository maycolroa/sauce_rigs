<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauDmQualificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_dm_qualification_types', function (Blueprint $table) {
            $table->string('type_input')->nullable()->after('description');
            $table->string('readonly')->default('NO')->after('type_input');
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
            $table->dropColumn('type_input');
            $table->dropColumn('readonly');

        });
    }
}
