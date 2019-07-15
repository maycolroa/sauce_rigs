<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSauLmLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->unsignedInteger('system_apply_id')->change();

            $table->foreign('system_apply_id')->references('id')->on('sau_lm_system_apply')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->dropForeign('sau_lm_laws_system_apply_id_foreign');
            $table->dropForeign('sau_lm_laws_company_id_foreign');
            //$table->string('system_apply_id')->change();
        });
    }
}
