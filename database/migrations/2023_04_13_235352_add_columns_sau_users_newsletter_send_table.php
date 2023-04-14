<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsSauUsersNewsletterSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_users_newsletter_send', function (Blueprint $table) {
            $table->unsignedInteger('newsletter_id')->after('email')->nullable();
            $table->boolean('state')->default(false)->after('newsletter_id');

            $table->foreign('newsletter_id')->references('id')->on('sau_newsletters_sends')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_users_newsletter_send', function (Blueprint $table) {
            $$table->dropForeign('sau_users_newsletter_send_newsletter_id_foreign');
            $table->dropColumn('newsletter_id');
            $table->dropColumn('state');
        });
    }
}
