<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnModuleSauCtFileUploadLessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {            
        $table->string('module')->nullable()->after('state');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {            
        $table->dropColumn('module');
        });
    }
}
