<?php

namespace App\Jobs\System\Licenses;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Models\General\Module;
use App\Models\General\Team;
use App\Models\System\Licenses\License;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\Configuration;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\SyncRestrictionDefaultJob;
use DB;

class NotifyLicenseRenewalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $license_id;
    protected $company_id;
    protected $modules;
    protected $mails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($license_id, $company_id, $modules, $mails)
    {
        $this->license_id = $license_id;
        $this->company_id = $company_id;
        $this->modules = $modules;
        $this->mails = $mails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $team = Team::where('name', $this->company_id)->first();
        $company = Company::find($this->company_id);

        $recipients = User::where('id', -1)->get();

        $emailAdmins = Configuration::getConfiguration('admin_license_notification_email');
        $emailAdmins = explode(",", $emailAdmins);

        foreach ($emailAdmins as $key => $value)
        {
            $recipients->push(new User(['email'=>$value]));
        }

        foreach ($this->mails as $key => $value)
        {
            $recipients->push(new User(['email'=>$value]));
        }

        $users = User::select('sau_users.email')
            ->active()
            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->join('sau_role_user', function($q) use ($team) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                  ->on('sau_role_user.team_id', '=', DB::raw($team->id));
            })
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->join('sau_permission_role', 'sau_permission_role.role_id', 'sau_roles.id')
            ->join('sau_permissions', 'sau_permissions.id', 'sau_permission_role.permission_id')
            //->where('sau_company_user.company_id', $this->company_id)
            //->where('sau_roles.company_id', $this->company_id)
            ->whereIn('sau_permissions.module_id', $this->modules)
            ->groupBy('sau_users.id', 'sau_users.email', 'sau_roles.display_name');

        $users->company_scope = $this->company_id;
        $users = $users->get();

        foreach ($users as $key => $value)
        {
            $recipients->push(new User(['email'=>$value]));
        }

        $modules_news = collect([]);
        $modules_olds = collect([]);

        foreach ($this->modules as $module)
        {
            $exists = License::select('sau_modules.display_name AS display_name')
                ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
                ->join('sau_modules', 'sau_modules.id', 'sau_license_module.module_id')
                ->where('sau_licenses.id', '<>', $this->license_id)
                ->where('sau_modules.id', $module);

            $exists->company_scope = $this->company_id;
            $exists = $exists->first();

            if ($exists)
                $modules_olds->push($exists->display_name);
            else
            {
                $exists = Module::find($module);
                $modules_news->push($exists->display_name);
            }
        }


        NotificationMail::
            subject('Creación de Licencia Sauce')
            ->recipients($recipients)
            ->message("Se acaba de crear una nueva licencia para la empresa <b>{$company->name}</b>")
            ->module('users')
            ->event('Job: NotifyLicenseRenewalJob')
            ->company($this->company_id)
            ->view("system.license.notificationLicense")
            ->with(['modules_news'=>$modules_news, 'modules_olds'=>$modules_olds])
            ->send();

        if (in_array(Module::where('name', 'reinstatements')->first()->id, $this->modules))
            SyncRestrictionDefaultJob::dispatch($this->company_id);
    }
}
