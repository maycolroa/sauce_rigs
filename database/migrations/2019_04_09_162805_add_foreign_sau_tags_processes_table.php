<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignSauTagsProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_tags_processes', function (Blueprint $table) {
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
        Schema::table('sau_tags_processes', function (Blueprint $table) {
            $table->dropForeign('sau_tags_processes_company_id_foreign');
        });
    }
}
