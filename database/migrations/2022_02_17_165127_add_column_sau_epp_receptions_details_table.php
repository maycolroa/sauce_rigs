<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSauEppReceptionsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_receptions_details', function (Blueprint $table) {
            $table->string('quantity_complete')->after('quantity_reception')->default('SI');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_receptions_details', function (Blueprint $table) {
            $table->dropColumn('quantity_complete');
        });
    }
}
