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
use App\Jobs\IndustrialSecure\AccidentsWork\SyncCategoryItemsDefaultJob;
use DB;
use App\Jobs\IndustrialSecure\RoadSafety\TypeVehiclesDefaultJob;

class NotifyLicenseRenewalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $license_id;
    protected $company_id;
    protected $modules;
    protected $mails;
    protected $asunto;
    protected $modify;
    protected $modules_delete;
    protected $freeze;
    protected $observations;
    protected $modules_freeze;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($license_id, $company_id, $modules, $mails, $asunto, $modify = [], $modules_delete = [], $freeze = null, $observations = '', $modules_freeze = [])
    {
        $this->license_id = $license_id;
        $this->company_id = $company_id;
        $this->modules = $modules;
        $this->mails = $mails;
        $this->asunto = $asunto;
        $this->modify = $modify;
        $this->modules_delete = $modules_delete;
        $this->freeze = $freeze;
        $this->observations = $observations;
        $this->modules_freeze = $modules_freeze;
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

        $admins = collect([]);

        $emailAdmins = Configuration::getConfiguration('admin_license_notification_email');
        $emailAdmins = explode(",", $emailAdmins);

        foreach ($emailAdmins as $key => $value)
        {
            $admins->push(['email'=>$value]);
        }

        foreach ($this->mails as $key => $value)
        {
            $recipients->push(new User(['email'=>$value]));
        }

        $supers = User::select('sau_users.email')
        ->active(true, $this->company_id)
        ->join('sau_role_user', function($q) use ($team) { 
            $q->on('sau_role_user.user_id', '=', 'sau_users.id')
            ->on('sau_role_user.team_id', '=', DB::raw($team->id));
        })
        ->join('sau_roles', 'sau_roles.id', 'sau_role_user.role_id')
        ->where('sau_roles.display_name', 'Superadmin')
        ->get();

        $limit = 50 - $supers->count() - count($admins);

        $modules_news = collect([]);
        $modules_olds = collect([]);
        $modules_f = collect([]);

        if ($this->freeze == 'NO')
        {
            $users = User::select('sau_users.email')
                ->active(true, $this->company_id)
                //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
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
                ->where('sau_roles.display_name', '<>', 'Superadmin')
                ->groupBy('sau_users.id', 'sau_users.email'/*, 'sau_roles.display_name'*/)
                ->limit($limit);

            $users->company_scope = $this->company_id;
            $users = $users->get();

            foreach ($users as $key => $value)
            {
                $recipients->push(new User(['email'=>$value->email]));
            }
        }
        else
        {
            foreach ($this->modules_freeze as $module)
            {
                $exists = License::select('sau_modules.display_name AS display_name')
                    ->join('sau_license_module_freeze', 'sau_license_module_freeze.license_id', 'sau_licenses.id')
                    ->join('sau_modules', 'sau_modules.id', 'sau_license_module_freeze.module_id')
                    ->where('sau_licenses.id', $this->license_id)
                    ->where('sau_modules.id', $module);
    
                $exists->company_scope = $this->company_id;
                $exists = $exists->first();
    
                if ($exists)
                    $modules_f->push($exists->display_name);
            }
        }

        foreach ($supers as $key => $value)
        {
            $admins->push(['email'=>$value->email]);
        }

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

        $fechas_modificadas = [];

        if (COUNT($this->modify) == 2)
        {
            foreach ($this->modify as $key => $value) 
            {
                if ($key == 0)
                    array_push($fechas_modificadas, 'Fecha Inicio: '.$value['fecha_inicio']);
                else
                    array_push($fechas_modificadas, 'Fecha Fin: '.$value['fecha_fin']);
            }
        }
        else if (COUNT($this->modify) == 1)
        {
            foreach ($this->modify as $key => $value) 
            {
                foreach ($value as $key => $value2) {
                    if ($key == 'fecha_inicio')
                        array_push($fechas_modificadas, 'Fecha Inicio: '.$value2);
                    else
                        array_push($fechas_modificadas, 'Fecha Fin: '.$value2);
                }
            }
        }

        if (COUNT($recipients) > 0 && COUNT($admins) > 0)
        {
            if ($this->freeze == 'SI')
            {
                $mod_total = ['modules_news'=> [], 'modules_olds'=> [], 'modify' => $fechas_modificadas, 'modules_delete' => [], 'modules_freeze' => $modules_f];
            }
            else 
            {
                $mod_total = ['modules_news'=>$modules_news, 'modules_olds'=>$modules_olds, 'modify' => $fechas_modificadas, 'modules_delete' => $this->modules_delete, 'modules_freeze' => $modules_f];
            }
            
            NotificationMail::
                subject($this->asunto .' de Licencia Sauce')
                ->recipients($recipients)
                ->message($this->asunto == 'Creación' ? "Se acaba de crear una licencia para la empresa <b>{$company->name}</b>" : ($this->freeze == 'NO' ? "Se acaba de modificar una licencia para la empresa <b>{$company->name}</b>" :"Se acaba de congelar una licencia para la empresa <b>{$company->name}</b>, con las siguientes observaciones <b>{$this->observations}</b>"))
                ->module('users')
                ->event('Job: NotifyLicenseRenewalJob')
                ->company($this->company_id)
                ->view("system.license.notificationLicense")
                ->with($mod_total)
                ->copyHidden($admins)
                ->send();
        }
        else
        {
            if ($this->freeze == 'SI')
            {
                $mod_total = ['modules_news'=> [], 'modules_olds'=> [], 'modify' => $fechas_modificadas, 'modules_delete' => [], 'modules_freeze' => $modules_f];
            }
            else 
            {
                $mod_total = ['modules_news'=>$modules_news, 'modules_olds'=>$modules_olds, 'modify' => $fechas_modificadas, 'modules_delete' => $this->modules_delete, 'modules_freeze' => $modules_f];
            }

            NotificationMail::
                subject($this->asunto .' de Licencia Sauce')
                ->message($this->asunto == 'Creación' ? "Se acaba de crear una licencia para la empresa <b>{$company->name}</b>" : ($this->freeze == 'NO' ? "Se acaba de modificar una licencia para la empresa <b>{$company->name}</b>" :"Se acaba de congelar una licencia para la empresa <b>{$company->name}</b>, con las siguientes observaciones <b>{$this->observations}</b>"))
                ->module('users')
                ->event('Job: NotifyLicenseRenewalJob')
                ->company($this->company_id)
                ->view("system.license.notificationLicense")
                ->with($mod_total)
                ->copyHidden($admins)
                ->send();
        }

        if (in_array(Module::where('name', 'reinstatements')->first()->id, $this->modules))
            SyncRestrictionDefaultJob::dispatch($this->company_id);
        if (in_array(Module::where('name', 'dangerousConditions')->first()->id, $this->modules))
            SyncCategoryItemsDefaultJob::dispatch($this->company_id);
        if (in_array(Module::where('name', 'roadSafety')->first()->id, $this->modules))
            TypeVehiclesDefaultJob::dispatch($this->company_id);
    }
}
