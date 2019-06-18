<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Configuration;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;

class DisableUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disableUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deshabilitar y notificar a los usuarios que tengan más de X días (Configurados en base de datos) sin iniciar sesión';

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
            ->whereRaw("DATEDIFF(CURDATE(), sau_users.last_login_at) >= ".$days_suspension)
            ->get();

        foreach ($users as $key => $user)
        {
            $user->update(['active' => 'NO']);

            NotificationMail::
                subject('Usuario suspendido')
                ->recipients($user)
                ->message('Estimado usuario '.$user->name.' su usuario ha sido suspendido por inactividad.')
                ->module('users')
                ->event('Tarea programada: DisableUsers')
                ->send();

            sleep(10);
        }
    }
}
