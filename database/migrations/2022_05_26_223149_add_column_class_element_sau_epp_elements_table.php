<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnClassElementSauEppElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {
            $table->string('class_element')->after('name')->default('Elemento de protección personal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {
            $table->dropColumn('class_element');
        });
    }
}
