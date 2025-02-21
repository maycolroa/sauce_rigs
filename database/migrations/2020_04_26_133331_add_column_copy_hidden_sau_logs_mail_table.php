<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCopyHiddenSauLogsMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_log_mails', function (Blueprint $table) {
            $table->text('copy_hidden')->after('recipients')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_log_mails', function (Blueprint $table) {
            $table->dropColumn('copy_hidden');
        });
    }
}
