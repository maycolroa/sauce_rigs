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
            ->where('sau_license_module.module_id', '21'); //32 prod

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $expired_reports = [];

            $keywords = $this->getKeywordQueue($company);

            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;

            \Log::info('entro');
            \Log::info($company);

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
                DB::raw("DATE_FORMAT(sau_reinc_checks.created_at, '%Y-%m-%d') as created_at")
            )
            ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
            ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
            ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
            ->isOpen();

            $reports->company_scope = $company;
            $reports = $reports->get();

            foreach ($reports as $key => $check) 
            { 
                $now = Carbon::now();

                $diff = $now->diffInDays($check->created_at);

                if ($diff >= $configDay)
                {
                    $content = [
                        'Empleado' => $check->name,
                        $keywords['disease_origin'] => $check->disease_origin,
                        'Codigo CIE' => $check->code,
                        'Descripción CIE' => $check->dx,
                        'Fecha' => Carbon::createFromFormat('D M d Y', $check->created_at)->format('Y-m-d'),
                        $keywords['regional'] => $check->regional,
                        'Estado' => $check->state
                    ];

                    array_push($expired_reports, $content);
                }
            }

            if (count($expired_reports) > 0)
            {
                $responsibles = ConfigurationsCompany::company($company)->findByKey('users_notify_expired_report');

                if ($responsibles)
                    $responsibles = explode(',', $responsibles);

                if (count($responsibles) > 0)
                {
                    foreach ($responsibles as $email)
                    {
                        $recipient = new User(["email" => $email]); 

                        NotificationMail::
                            subject('Sauce - Reincorporaciones Reportes')
                            ->recipients($recipient)
                            ->message("Este es el listado de empleados con reportes abiertos desde hace mas de <b>$configDay</b> dias.")
                            ->module('reinstatements')
                            ->event('Tarea programada: NotifyReportsOpenConfig')
                            ->table($expired_reports)
                            ->company($company)
                            ->send();
                    }
                }
            }
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
                return $days;
            }
            else
                return NULL;
        } catch (\Exception $e) {
            return NULL;
        }

    }
}
