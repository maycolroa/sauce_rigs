<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStateSauCtFileUploadContractsLeesseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_file_upload_contracts_leesse', function ($table) {
            $table->string('state')->after('user_id')->default('PENDIENTE');
            $table->text('reason_rejection')->after('state')->nullable();
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
            $table->dropColumn('state');
            $table->dropColumn('reason_rejection');
        });
    }
}
