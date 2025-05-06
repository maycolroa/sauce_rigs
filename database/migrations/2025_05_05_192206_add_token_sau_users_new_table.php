<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Administrative\Users\User;

class AddTokenSauUsersNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::whereNull('api_token')->get();

        foreach ($users as $key => $user)
        {
            $user->forceFill([
                'api_token' => Hash::make($user->document . str_random(10))
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
        //
    }
}
