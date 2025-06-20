<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNomenclatureSauRmRiskMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rm_risks_matrix', function (Blueprint $table) {
            $table->string('nomenclature')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rm_risks_matrix', function (Blueprint $table) {
            $table->dropColumn('nomenclature');
        });
    }
}
