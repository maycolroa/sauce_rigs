<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\Administrative\Employees\Employee;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\Configuration;
use Carbon\Carbon;

class DaysAlertExpiredLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'days-alert-expired-license';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a los usuarios de que se acerca el vencimiento de su licencia';

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
        $dayReference = Configuration::getConfiguration('days_alert_expired_license');

        $now = Carbon::now();
        $dateReference = Carbon::now()->addDays($dayReference);

        $licenses = License::system()->notNotified()->with('company')
        ->where([
            ['ended_at', '>', $now],
            ['ended_at', '<', $dateReference]
        ])->get();

        if (!$licenses->isEmpty()) {
            $emailToNotify = Configuration::getConfiguration('admin_license_notification_email');

            $data = [];

            foreach ($licenses as $key => $value)
            {
                $value->notified = 'SI';
                $value->save();

                $modules = [];

                foreach ($value->modules as $module)
                {
                    array_push($modules, $module->display_name);
                }
                
                array_push($data, [
                    'Compañia' => $value->company->name,
                    'Fecha Vencimiento' => Carbon::parse($value->ended_at)->toDateString(),
                    'Módulos' => implode(', ', $modules)
                ]);
            }

            NotificationMail::
                subject('Vencimiento de Licencias Próximo')
                ->view('notification')
                ->recipients(new Employee(['email'=>$emailToNotify]))
                ->message('Las siguientes licencias están próximas a vencerse: ')
                ->module('licenses')
                ->event('Tarea programada: DaysAlertExpiredLicense')
                ->table($data)
                ->company(1)
                ->send();
        }
    }
}
