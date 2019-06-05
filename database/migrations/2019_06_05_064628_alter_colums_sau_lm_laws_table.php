<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumsSauLmLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_laws', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->text('observations')->nullable()->change();
            $table->string('file')->nullable()->change();
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
            $table->text('description')->change();
            $table->text('observations')->change();
            $table->string('file')->change();
        });
    }
}
