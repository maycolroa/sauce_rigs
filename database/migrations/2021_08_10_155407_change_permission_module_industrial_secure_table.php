<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\General\Module;
use App\Models\General\Permission;

class ChangePermissionModuleIndustrialSecureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modules_dm = [12,13];
        $permissions_dm = Permission::whereIn("module_id", $modules_dm)
        ->get();

        foreach ($permissions_dm as $value) 
        {
            $value->update([
                'module_id' => 14
            ]);
        }

        $modules_rm = [32,33];
        $permissions_rm = Permission::whereIn("module_id", $modules_rm)
        ->get();

        foreach ($permissions_rm as $value) 
        {
            $value->update([
                'module_id' => 31
            ]);
        }

        Module::whereIn('id', [12,13,32,33])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Log::info('rollback');
    }
}
