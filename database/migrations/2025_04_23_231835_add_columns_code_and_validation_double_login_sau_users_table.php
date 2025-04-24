<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsCodeAndValidationDoubleLoginSauUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_users', function (Blueprint $table) {   
            $table->string('code_login')->nullable();
            $table->boolean('validate_login')->default(false)->after('code_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_users', function (Blueprint $table) {            
            $table->dropColumn('code_login');               
            $table->dropColumn('validate_login');      
        });
    }
}
