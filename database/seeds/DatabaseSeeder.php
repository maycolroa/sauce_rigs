<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(ApplicationsSeeder::class);
        $this->call(ModulesSeeder::class);
        $this->call(SauConfiguration::class);
        $this->call(MakeAllPermissionsSeeder::class);
    }
}
