<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsSauAbsenFileUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_absen_file_upload', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->after('name');
            $table->text('path')->after('company_id');
            $table->string('state')->default('Pendiente')->after('user_id');
            $table->string('file')->nullable()->change();
            $table->integer('talend_id')->unsigned()->after('state');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('talend_id')->references('id')->on('sau_absen_talends')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_absen_file_upload', function (Blueprint $table) {
            $table->dropForeign('sau_absen_file_upload_company_id_foreign');
            $table->dropColumn('company_id');
            $table->dropForeign('sau_absen_file_upload_talend_id_foreign');
            $table->dropColumn('talend_id');
            $table->dropColumn('path');
            $table->dropColumn('state');
        });
    }
}
