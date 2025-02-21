<?php

use Illuminate\Database\Seeder;
use App\Models\Administrative\Users\User;

class CreatedApiTokenUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereNull('api_token')->get();

        foreach ($users as $key => $user)
        {
            $user->forceFill([
                'api_token' => Hash::make($user->document . str_random(10))
            ])->save();
        }
    }
}
