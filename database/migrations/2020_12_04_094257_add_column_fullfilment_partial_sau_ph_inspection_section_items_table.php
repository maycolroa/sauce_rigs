<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFullfilmentPartialSauPhInspectionSectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspection_section_items', function (Blueprint $table) {
            $table->double('compliance_value')->after('inspection_section_id')->nullable();
            $table->double('partial_value')->after('compliance_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_inspection_section_items', function (Blueprint $table) { 
            $table->dropColumn('compliance_value');
            $table->dropColumn('partial_value');
        });
    }
}
