<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNotificateSauCtFileUploadContractsLeesseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {            
            $table->boolean('notificate')->default(false);
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
            $table->dropColumn('notificate');
        });
    }
}
