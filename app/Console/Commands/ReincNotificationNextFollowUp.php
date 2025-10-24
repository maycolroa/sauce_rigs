<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Facades\Mail\Facades\NotificationMail;
use DB;

class ReincNotificationNextFollowUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinc-notification-next-follow-up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía una notificación a los usuarios de la empresa con los reportes que estén a 3 días del próximo seguimiento';

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
        $companies = DB::table('sau_reinc_checks')
            ->selectRaw('DISTINCT company_id AS id')
            ->get();

        foreach ($companies as $company)
        {
            $users = User::select('sau_users.*')
                        ->active(true, $company->id)
                        ->withoutGlobalScopes()
                        ->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('reinc_receive_notifications', $company->id) && !$user->isSuperAdmin($company->id);
            });

            $users->map(function($user) use ($company)
            {
                $headquarters = $this->getHeadquarters($user, $company->id);

                $data = Check::select(
                    'sau_reinc_checks.disease_origin', 
                    'sau_reinc_checks.next_date_tracking', 
                    'sau_employees.identification', 
                    'sau_employees.name'
                )
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->isOpen()
                ->whereRaw('CURDATE() = DATE_ADD(sau_reinc_checks.next_date_tracking, INTERVAL -3 DAY)');
        
                if (count($headquarters) > 0)
                {
                    $headquarters2 = implode(',', $headquarters);
                    $data->whereRaw("sau_employees.employee_headquarter_id in ({$headquarters2})");
                }

                $data->user = $user->id;
                $data->company_scope = $company->id;
                $data = $data->get();

                if (COUNT($data) > 0)
                {
                    $table = [];

                            foreach ($data as $value)
                            {
                                array_push($table, [
                                    'Identificacion' => $value->identification,
                                    'Nombre' => $value->name,
                                    'Origen de enfermedad' => $value->disease_origin,
                                    'Proximo seguimiento' => $value->next_day_tracking
                                ]);
                            }

                            NotificationMail::
                                subject('Reincorporaciones: Reportes con próximos seguimientos')
                                ->view('notification')
                                ->recipients($user)
                                ->message('Listado de personas con reportes que estan a 3 dias para su próximo seguimmiento ')
                                ->module('reinstatements')
                                ->event('Tarea programada: ReincNotificationNextFollowUp')
                                ->table($table)
                                ->company($company->id)
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
}
