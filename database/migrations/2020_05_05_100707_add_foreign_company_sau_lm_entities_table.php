<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignCompanySauLmEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_lm_entities', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('name')->nullable();

            $table->foreign('company_id')
                ->references('id')
                ->on('sau_companies')
                ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_lm_entities', function (Blueprint $table) {
            $table->dropForeign('company_id');
        });
    }
}
