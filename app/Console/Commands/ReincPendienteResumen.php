<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use DB;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use Illuminate\Database\Eloquent\Builder;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Carbon\Carbon;


class ReincPendienteResumen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinc-pendiente-resumen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resumen de actividades pendientes en el mes en reincorporaciones';

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
            ->where('sau_license_module.module_id', '21');

        $now = Carbon::now();

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $company)
        {
            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;

            /*$checks = DB::table('sau_reinc_checks')
            ->selectRaw('DISTINCT company_id AS id')
            ->where('company_id', $company)
            ->get();*/

            $users = User::select('sau_users.*')
            ->withoutGlobalScopes()
            ->active(true, $company)
            //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->where('sau_company_user.company_id', $company)
            ->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('reinc_receive_notifications', $company) && !$user->isSuperAdmin($company);
            });

            $users->map(function($user) use ($company, $now)
            {
                $headquarters = $this->getHeadquarters($user, $company);//User::find($user->id)->headquarters()->pluck('id')->toArray();
                \Log::info($headquarters);

                $data = Check::select(
                    'sau_reinc_checks.company_id',
                    'sau_employees.identification',
                    'sau_employees.name',
                    'sau_employees.employee_headquarter_id as sede',
                    DB::raw('MIN(monitoring_recommendations) AS monitoring_recommendations'),
                    DB::raw('MIN(end_recommendations) AS end_recommendations'),
                    DB::raw('MIN(sau_reinc_medical_monitorings.set_at) AS medical_monitoring'),
                    DB::raw('MIN(sau_reinc_labor_monitorings.set_at) AS laboral_monitoring')
                )
                ->withoutGlobalScopes()
                ->isOpen()
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->leftJoin('sau_reinc_medical_monitorings', 'sau_reinc_medical_monitorings.check_id', 'sau_reinc_checks.id')
                ->leftJoin('sau_reinc_labor_monitorings', 'sau_reinc_labor_monitorings.check_id', 'sau_reinc_checks.id')
                ->whereRaw("
                    (year(monitoring_recommendations) = $now->year and month(monitoring_recommendations) = $now->month) 
                    or (year(end_recommendations) = $now->year and month(end_recommendations) = $now->month)
                    or (year(sau_reinc_medical_monitorings.set_at) = $now->year and month(sau_reinc_medical_monitorings.set_at) = $now->month)
                    or (year(sau_reinc_labor_monitorings.set_at) = $now->year and month(sau_reinc_labor_monitorings.set_at) = $now->month)"
                )
                ->where('sau_reinc_checks.company_id', $company);

                if (count($headquarters) > 0)
                {
                    $headquarters2 = implode(',', $headquarters);
                    $data->whereRaw("sau_employees.employee_headquarter_id in ({$headquarters2})");
                }
                /*->whereRaw('(
                    CURDATE() = DATE_ADD(monitoring_recommendations, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(end_recommendations, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(sau_reinc_medical_monitorings.set_at, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(sau_reinc_labor_monitorings.set_at, INTERVAL -7 DAY) )'
                )*/
                $data->groupBy([
                    'sau_reinc_checks.company_id',
                    'sau_employees.identification',
                    'sau_employees.name',
                    'sau_employees.employee_headquarter_id'
                ]);

                $data->user = $user->id;
                //$data->company_scope = $company;
            
                $data = DB::table(DB::raw("({$this->getSqlWithBinding($data)}) AS t"))
                ->select(
                    'company_id',
                    'identification', 
                    'name', 
                    'sede',
                    /*DB::raw("CONCAT(
                        CASE WHEN CURDATE() = DATE_ADD(monitoring_recommendations, INTERVAL -7 DAY) THEN 'Seguimiento de las recomendaciones. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(end_recommendations, INTERVAL -7 DAY) THEN 'Finalizacion de las recomendaciones. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(medical_monitoring, INTERVAL -7 DAY) THEN 'Seguimiento Medico. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(laboral_monitoring, INTERVAL -7 DAY) THEN 'Seguimiento Laboral. \n' ELSE '' END) AS message")*/
                    DB::raw("CONCAT(
                        CASE WHEN year(monitoring_recommendations) = $now->year and month(monitoring_recommendations) = $now->month THEN 'Seguimiento de las recomendaciones. ' ELSE '' END,
                        CASE WHEN year(end_recommendations) = $now->year and month(end_recommendations) = $now->month THEN 'Finalizacion de las recomendaciones. ' ELSE '' END,
                        CASE WHEN year(medical_monitoring) = $now->year and month(medical_monitoring) = $now->month THEN 'Seguimiento Medico. ' ELSE '' END,
                        CASE WHEN year(laboral_monitoring) = $now->year and month(laboral_monitoring) = $now->month THEN 'Seguimiento Laboral.' ELSE '' END) AS message")
                    )
                    ->where('company_id', $company);

                    if (count($headquarters) > 0)
                    {
                        $headquarters22 = implode(',', $headquarters);
                        $data->whereRaw("sede in ({$headquarters22})");
                    }

                    $data = $data->get();
                    
                    if (COUNT($data) > 0)
                    {
                        $table = [];

                        foreach ($data as $value)
                        {
                            array_push($table, [
                                'Identificacion' => $value->identification,
                                'Nombre' => $value->name,
                                'Eventos' => $value->message
                            ]);
                        }

                        NotificationMail::
                            subject('Reincorporaciones: Eventos pendientes')
                            ->view('notification')
                            ->recipients($user)
                            ->message('Listado de personas con eventos que se cumpliaran este mes')
                            ->module('reinstatements')
                            ->event('Tarea programada: ReincPendienteResumen')
                            ->table($table)
                            ->company($company)
                            ->send();
                    }     
                });
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
            ->where('sau_reinc_user_headquarter.user_id', $user->id)
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

    public static function getSqlWithBinding(Builder $query): string 
    {
        $sql = $query->toSql();
        foreach ($query->getBindings() as $binding)
        {
            $value = is_numeric($binding) ? $binding : '\'' . $binding . '\'';
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    public function getConfig($company_id)
    {
        $key = "reports_resumen_month";

        try
        {
            $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

            if ($exists && $exists == 'SI')
            {
                \Log::info('SI');
                return true;
            }
            else
                return NULL;
        } catch (\Exception $e) {
            \Log::info('NO');
            return NULL;
        }

    }
}
