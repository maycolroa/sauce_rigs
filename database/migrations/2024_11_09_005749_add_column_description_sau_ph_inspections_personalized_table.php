<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDescriptionSauPhInspectionsPersonalizedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_ph_inspections', function (Blueprint $table) {            
        $table->text('description')->nullable()->after('state');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sau_ph_inspections', function (Blueprint $table) {            
        $table->dropColumn('description');
        });
    }
}
