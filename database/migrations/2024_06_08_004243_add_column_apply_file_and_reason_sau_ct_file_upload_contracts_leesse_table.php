<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnApplyFileAndReasonSauCtFileUploadContractsLeesseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ct_file_upload_contracts_leesse', function (Blueprint $table) {
            $table->string('apply_file')->nullable()->after('reason_rejection')->default('SI');
            $table->text('apply_motive')->nullable()->after('apply_file');
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
            $table->dropColumn('apply_file');
            $table->dropColumn('apply_motive');
        });
    }
}
