<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesPivotNotificationsMasiveContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_notification_activities', function (Blueprint $table) {            
            $table->unsignedInteger('notification_id');
            $table->foreign('notification_id')->references('id')->on('sau_ct_send_notifications')->onDelete('cascade');

            $table->unsignedInteger('activity_id');
            $table->foreign('activity_id')->references('id')->on('sau_ct_activities')->onDelete('cascade');
        });

        Schema::create('sau_ct_notification_contracts', function (Blueprint $table) {            
            $table->unsignedInteger('notification_id');
            $table->foreign('notification_id')->references('id')->on('sau_ct_send_notifications')->onDelete('cascade');

            $table->unsignedInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('sau_user_information_contract_lessee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_notification_activities');
        Schema::dropIfExists('sau_ct_notification_contracts');
    }
}
