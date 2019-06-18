<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Configuration;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;

class CheckLastLoginNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkLastLoginNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica cuando fue la última vez que iniciarón sesión los usuarios y en el caso de tener más de X días (Configurados en base de datos) se les enviara una notificación de alerta de suspensión';

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
        $days_alert = Configuration::getConfiguration('days_alert_user_suspension');
        $days_suspension = Configuration::getConfiguration('days_user_suspension');

        $users = User::active()
            ->whereRaw("CURDATE() = DATE_ADD(sau_users.last_login_at, INTERVAL +".$days_alert." DAY)")
            ->get();

        foreach ($users as $key => $user)
        {
            NotificationMail::
                subject('Alerta de suspensión de usuario')
                ->recipients($user)
                ->message('Estimado usuario '.$user->name.' se ha detectado que su usuario tiene aproximadamente '.$days_alert.' dias sin iniciar sesión en el sistema, si en los próximos '.($days_suspension - $days_alert).' dias no ha iniciado sesión su usuario será suspendido automáticamente.')
                ->buttons([['text'=>'Ir al sitio', 'url'=>url("/login")]])
                ->module('users')
                ->event('Tarea programada: CheckLastLoginNotification')
                ->send();

            sleep(10);
        }
    }
}
