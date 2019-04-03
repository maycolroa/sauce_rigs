<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFulfillmentSauCtQualifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_qualifications', function (Blueprint $table) {
            $table->integer('fulfillment')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_qualifications', function (Blueprint $table) {
            $table->dropColumn('fulfillment');
        });
    }
}
