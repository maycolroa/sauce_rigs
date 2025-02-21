<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStateValidationQualificationSauCtListCheckQualificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_item_qualification_contract', function (Blueprint $table) {
            $table->string('state_aprove_qualification')->after('observations')->nullable();
            $table->text('reason_rejection')->after('state_aprove_qualification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_item_qualification_contract', function (Blueprint $table) {
            $table->dropColumn('state_aprove_qualification');
            $table->dropColumn('reason_rejection');
        });
    }
}
