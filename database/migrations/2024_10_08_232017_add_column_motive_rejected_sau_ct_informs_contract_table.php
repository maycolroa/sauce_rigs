<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMotiveRejectedSauCtInformsContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_inform_contract', function (Blueprint $table) {            
            $table->text('motive_rejected')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_inform_contract', function (Blueprint $table) {
            $table->dropColumn('motive_rejected');
        });
    }
}
