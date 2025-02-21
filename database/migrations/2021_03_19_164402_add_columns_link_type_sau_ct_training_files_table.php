<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsLinkTypeSauCtTrainingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_training_files', function ($table) {
            $table->string('type')->after('training_id')->default('Archivo');
            $table->string('link')->after('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_training_files', function ($table) {
            $table->dropColumn('type');
            $table->dropColumn('link');
        });
    }
}
