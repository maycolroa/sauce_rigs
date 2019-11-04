<?php

namespace App\Jobs\System\Companies;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Team;
use App\Models\General\Company;
use DB;

class SyncUsersSuperadminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$team = Team::where('name', $this->company_id)->first();
        $companies = Company::pluck('id')->toArray();
        $role = Role::defined()->where('name', 'Superadmin')->first();
        $users = User::select(
            DB::raw('DISTINCT(sau_role_user.user_id) AS id')
        )
        ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
        ->where('role_id', $role->id)
        ->get();

        foreach($users as $user)
        {
            $user->companies()->withoutGlobalScopes()->sync($companies);
        }

        foreach ($companies as $company)
        {
            $team = Team::where('name', $company)->first();

            if ($team)
            {
                foreach ($users as $user) 
                {
                    $user->syncRoles([$role], $team);
                }
            }
        }
    }
}
