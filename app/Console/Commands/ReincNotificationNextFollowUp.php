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
                        ->active()
                        ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');

            $users->company_scope = $company->id;
            $users = $users->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('reinc_receive_notifications', $company->id);
            });

            $users->map(function($user) use ($company)
            {
                $data = Check::select(
                    'sau_reinc_checks.disease_origin', 
                    'sau_reinc_checks.next_date_tracking', 
                    'sau_employees.identification', 
                    'sau_employees.name'
                )
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->isOpen()
                ->whereRaw('CURDATE() = DATE_ADD(sau_reinc_checks.next_date_tracking, INTERVAL -3 DAY)');
        
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
}
