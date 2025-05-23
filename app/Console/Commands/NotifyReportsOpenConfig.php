<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use Carbon\Carbon;
use DB;
use App\Traits\UtilsTrait;


class NotifyReportsOpenConfig extends Command
{
    use UtilsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-reports-open-config';
    protected $responsibles = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificacion sobre reportes abiertos con una cantidad de dias superior a la configurada';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
           // ->where('sau_licenses.company_id', 1)
            ->where('sau_license_module.module_id', '21');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            //$expired_reports = [];

            $keywords = $this->getKeywordQueue($company);

            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;
                
            $reports = Check::select(
                'sau_reinc_checks.id AS id',
                'sau_reinc_checks.deadline AS deadline',
                'sau_reinc_checks.next_date_tracking AS next_date_tracking',
                'sau_reinc_cie10_codes.code AS code',
                'sau_reinc_cie10_codes.description AS dx',
                'sau_reinc_checks.disease_origin AS disease_origin',
                'sau_employees_regionals.name AS regional',
                'sau_reinc_checks.state AS state',
                'sau_employees.identification AS identification',
                'sau_employees.name AS name',
                'sau_employees.employee_headquarter_id AS sede',
                'sau_employees_headquarters.name AS sede_name',
                DB::raw("DATE_FORMAT(sau_reinc_checks.monitoring_recommendations, '%Y-%m-%d') as created_at")
            )
            ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
            ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
            ->whereRaw("CURDATE() = DATE_ADD(monitoring_recommendations, INTERVAL -{$configDay} DAY)")
            //->where('sau_reinc_checks.company_id', 1)
            ->isOpen()
            ->groupBy([
                'sau_reinc_checks.company_id',
                'sau_employees.identification',
                'sau_employees.name',
                'sau_employees.employee_headquarter_id',
                'sau_reinc_checks.id'
            ]);

            $reports->company_scope = $company;
            $reports = $reports->get();

            if ($this->responsibles)
                $this->responsibles = explode(',', $this->responsibles);

            if (count($this->responsibles) > 0)
            {
                foreach ($this->responsibles as $email)
                {
                    $expired_reports = [];
                    $user = User::select('*')->active(true, $company)->withoutGlobalScopes()->where('email', $email)->first();

                    if ($user)
                    {
                        $headquarters = $this->getHeadquarters($user, $company);

                        foreach ($reports as $key => $check) 
                        { 
                            if (count($headquarters) > 0)
                            {
                                if (in_array($check->sede,$headquarters))
                                {
                                    $content = [
                                        'Empleado' => $check->name,
                                        'Tipo de Evento' => $check->disease_origin,
                                        'Codigo CIE' => $check->code,
                                        'Descripción CIE' => $check->dx,
                                        'Fecha' => Carbon::createFromFormat('D M d Y', $check->created_at)->format('Y-m-d'),
                                        'Sede' => $check->sede_name,
                                        'Estado' => $check->state
                                    ];

                                    array_push($expired_reports, $content);
                                }
                            }
                            else
                            {                                
                                $content = [
                                    'Empleado' => $check->name,
                                    'Tipo de Evento' => $check->disease_origin,
                                    'Codigo CIE' => $check->code,
                                    'Descripción CIE' => $check->dx,
                                    'Fecha' => Carbon::createFromFormat('D M d Y', $check->created_at)->format('Y-m-d'),
                                    'Sede' => $check->sede_name,
                                    'Estado' => $check->state
                                ];

                                array_push($expired_reports, $content);
                            }
                        }

                        if (count($expired_reports) > 0)
                        {
                            NotificationMail::
                                subject('Sauce - Reincorporaciones Reportes')
                                ->recipients($user)
                                ->message("Este es el listado de empleados con seguimientos a recomendaciones en los próximos <b>$configDay</b> dias.")
                                ->module('reinstatements')
                                ->event('Tarea programada: NotifyReportsOpenConfig')
                                ->view('preventiveoccupationalmedicine.reinstatements.notifyExpiredCheck')
                                ->with(['data'=>$expired_reports])
                                //->table($expired_reports)
                                ->company($company)
                                ->send();
                        }
                    }
                    else
                    {
                        continue;
                    }
                }
            }
        }
    }

    public function getHeadquarters($user, $company_id)
    {
        try
        {
            $headquarters_users = DB::table('sau_users')
            ->select('sau_employees_headquarters.*')
            ->join('sau_reinc_user_headquarter', 'sau_users.id', 'sau_reinc_user_headquarter.user_id')
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_reinc_user_headquarter.employee_headquarter_id')
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
            ->where('sau_reinc_user_headquarter.user_id', $user->user_id)
            ->where('sau_employees_regionals.company_id', $company_id)
            ->pluck('sau_employees_headquarters.id')
            ->toArray();

            if (!$headquarters_users)
                $headquarters_users = [];

            return $headquarters_users;
                
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return [];
        }

    }

    public function getConfig($company_id)
    {
        $key = "reports_opens_notify";
        $key1 = "days_alert_expiration_report_notify";

        try
        {
            $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

            if ($exists && $exists == 'SI')
            {
                $days = ConfigurationsCompany::company($company_id)->findByKey($key1);

                $this->responsibles = ConfigurationsCompany::company($company_id)->findByKey('users_notify_expired_report');
                
                return $days;
            }
            else
                return NULL;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return NULL;
        }

    }
}
