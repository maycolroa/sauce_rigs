<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStateGeneratePasswordUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_generate_password_user', function (Blueprint $table) {
            $table->string('state')->default('without use');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_generate_password_user', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
