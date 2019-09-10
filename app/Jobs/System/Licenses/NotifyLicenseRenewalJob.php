<?php

namespace App\Jobs\System\Licenses;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use App\Models\General\Module;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\Configuration;

class NotifyLicenseRenewalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;
    protected $modules;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id, $modules)
    {
      $this->company_id = $company_id;
      $this->modules = $modules;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipients = User::select('sau_users.email')
            ->active()
            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->join('sau_role_user', 'sau_role_user.user_id', 'sau_users.id')
            ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
            ->join('sau_permission_role', 'sau_permission_role.role_id', 'sau_roles.id')
            ->join('sau_permissions', 'sau_permissions.id', 'sau_permission_role.permission_id')
            ->where('sau_company_user.company_id', $this->company_id)
            //->where('sau_roles.company_id', $this->company_id)
            ->whereIn('sau_permissions.module_id', $this->modules)
            ->groupBy('sau_users.id', 'sau_users.email', 'sau_roles.display_name');

        $recipients->company_scope = $this->company_id;
        $recipients = $recipients->get();

        if (COUNT($recipients) > 0)
        {
            $emailAdmins = Configuration::getConfiguration('admin_license_notification_email');
            $emailAdmins = explode(",", $emailAdmins);

            foreach ($emailAdmins as $key => $value)
            {
                $recipients->push(new User(['email'=>$value]));
            }

            $modules = Module::selectRaw('DISTINCT display_name')
                            ->whereIn('id', $this->modules)
                            ->orderBy('display_name')
                            ->pluck('display_name')
                            ->toArray();

            $modules = implode(", ", $modules);

            NotificationMail::
                subject('Renovación de Licencia')
                ->recipients($recipients)
                ->message("Se ha renovado la licencia de su compañia para los siguientes módulos: {$modules}")
                ->module('users')
                ->event('Job: NotifyLicenseRenewalJob')
                ->company($this->company_id)
                ->send();
        }
    }
}
