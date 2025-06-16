<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Administrative\Roles\Role;

class InsertRoleDefinedMulticontratante extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call("db:seed --class=MakeCustomPermissionsSeeder");
        \Artisan::call("db:seed --class=CreateRoleSuperAdminSeeder");
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
