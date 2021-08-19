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

class ReincSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinc-send-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificacion cuando se vencen las recomendaciones de algun reporte de empleados en reincorporaciones';

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

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $company)
        {
            $checks = DB::table('sau_reinc_checks')
            ->selectRaw('DISTINCT company_id AS id')
            ->where('company_id', $company)
            ->get();

            $users = User::select('sau_users.*')
                            ->active()
                            ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');

            $users->company_scope = $company;
            $users = $users->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('reinc_receive_notifications', $company) && !$user->isSuperAdmin($company);
            });

            $users->map(function($user) use ($company)
            {
                $data = Check::select(
                    'sau_reinc_checks.company_id',
                    'sau_employees.identification',
                    'sau_employees.name',
                    DB::raw('MIN(monitoring_recommendations) AS monitoring_recommendations'),
                    DB::raw('MIN(end_recommendations) AS end_recommendations'),
                    DB::raw('MIN(sau_reinc_medical_monitorings.set_at) AS medical_monitoring'),
                    DB::raw('MIN(sau_reinc_labor_monitorings.set_at) AS laboral_monitoring')
                )
                ->isOpen()
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->leftJoin('sau_reinc_medical_monitorings', 'sau_reinc_medical_monitorings.check_id', 'sau_reinc_checks.id')
                ->leftJoin('sau_reinc_labor_monitorings', 'sau_reinc_labor_monitorings.check_id', 'sau_reinc_checks.id')
                ->whereRaw('(
                    CURDATE() = DATE_ADD(monitoring_recommendations, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(end_recommendations, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(sau_reinc_medical_monitorings.set_at, INTERVAL -7 DAY) 
                    or CURDATE() = DATE_ADD(sau_reinc_labor_monitorings.set_at, INTERVAL -7 DAY) )'
                )
                ->groupBy([
                    'sau_reinc_checks.company_id',
                    'sau_employees.identification',
                    'sau_employees.name'
                ]);

                $data->user = $user->id;
                $data->company_scope = $company;
            
                $data = DB::table(DB::raw("({$this->getSqlWithBinding($data)}) AS t"))
                ->select(
                    'company_id',
                    'identification', 
                    'name', 
                    DB::raw("CONCAT(
                        CASE WHEN CURDATE() = DATE_ADD(monitoring_recommendations, INTERVAL -7 DAY) THEN 'Seguimiento de las recomendaciones. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(end_recommendations, INTERVAL -7 DAY) THEN 'Finalizacion de las recomendaciones. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(medical_monitoring, INTERVAL -7 DAY) THEN 'Seguimiento Medico. \n' ELSE '' END,
                        CASE WHEN CURDATE() = DATE_ADD(laboral_monitoring, INTERVAL -7 DAY) THEN 'Seguimiento Laboral. \n' ELSE '' END) AS message")
                    )
                    ->get();
                    
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
                            ->message('Listado de personas con eventos que estan a 7 dias de cumplirse ')
                            ->module('reinstatements')
                            ->event('Tarea programada: ReincSendMail')
                            ->table($table)
                            ->company($company)
                            ->send();

                        \Log::info($user);
                        \Log::info($table);
                    }     
                });
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
}
