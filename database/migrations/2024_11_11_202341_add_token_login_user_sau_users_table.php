<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Administrative\Users\User;


class AddTokenLoginUserSauUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_users', function ($table) {
            $table->string('token_login', 80)->after('api_token')
                    ->unique()
                    ->nullable()
                    ->default(null);
        });

        $users = User::whereNull('token_login')->get();

        foreach ($users as $key => $user)
        {
            $tok = Hash::make($user->document . str_random(10));
            
            $user->forceFill([
                'token_login' => str_replace("/", "a", $tok)
            ])->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_users', function (Blueprint $table) {
            $table->dropColumn('token_login');
        });
    }
}
