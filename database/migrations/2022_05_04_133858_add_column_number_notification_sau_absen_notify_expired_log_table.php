<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNumberNotificationSauAbsenNotifyExpiredLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_absen_notify_expired_log', function (Blueprint $table) {
            $table->integer('number_notification')->after('email_send');
            $table->renameColumn('cod_dx', 'id_consecutivo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_absen_notify_expired_log', function (Blueprint $table) {
            $table->dropColumn('number_notification');
        });
    }
}
