<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeFileSauCtEvaluationItemFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_evaluation_item_files', function (Blueprint $table) {
            $table->string('type_file')->after('file')->default('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_evaluation_item_files', function (Blueprint $table) {
            $table->dropColumn('type_file');
        });
    }
}
