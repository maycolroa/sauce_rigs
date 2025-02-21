<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIdentifyDateExpirationSauEppElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_elements', function (Blueprint $table) {
            $table->boolean('identify_each_element')->after('image')->default(false);
            $table->boolean('expiration_date')->after('identify_each_element')->default(false);
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
            $table->dropColumn('identify_each_element');
            $table->dropColumn('expiration_date');
        });
    }
}
