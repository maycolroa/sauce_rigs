<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdaColumnSauEppDeliveryClassElementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_epp_transactions_employees', function (Blueprint $table) {
            $table->string('class_element')->after('type')->default('Elemento de protección personal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_epp_transactions_employees', function (Blueprint $table) {
            $table->dropColumn('class_element');
        });
    }
}
