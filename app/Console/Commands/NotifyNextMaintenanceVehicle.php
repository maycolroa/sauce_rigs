<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\IndustrialSecure\RoadSafety\Vehicle;
use App\Models\IndustrialSecure\RoadSafety\Maintenance;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\System\Licenses\License;
use DB;

class NotifyNextMaintenanceVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-next-maintenance-vehicle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía una notificación sobre los mantenimientos mas cercanos de vehiculos';

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
        ->where('sau_license_module.module_id', 39 /*39 prod, 38 local*/);

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $company)
        {
            $users = User::select('sau_users.*')
                        ->active(true, $company);
                        //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');

            $users->company_scope = $company;
            $users = $users->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('roadsafety_receive_notifications', $company) && !$user->isSuperAdmin($company);
            });


            $table = [];

            $vehicles = Vehicle::select('*');       
            $vehicles->company_scope = $company;
            $vehicles = $vehicles->get();

            foreach ($vehicles as $key => $vehicle) 
            {
                $maintenance = Maintenance::select('*')
                ->where('vehicle_id', $vehicle->id)
                ->whereRaw('CURDATE() = DATE_ADD(sau_rs_vehicle_maintenance.next_date, INTERVAL -5 DAY)')
                ->orderBy('id','DESC')
                ->first();


                if ($maintenance)
                {
                    array_push($table, [
                        'placa' => $vehicle->plate,
                        'Fecha de mantenimiento' => $maintenance->next_date
                    ]);
                }
            }

            if (COUNT($users) > 0)
            {
                foreach ($users as $key => $user) 
                {
                    NotificationMail::
                        subject('Seguridad Vial: Vehiculos con próximos mantenimientos')
                        ->view('notification')
                        ->recipients($user)
                        ->message('Listado de vehiculos a 5 dias para su próximo mantenimiento')
                        ->module('roadSafety')
                        ->event('Tarea programada: NotifyNextMaintenanceVehicle')
                        ->table($table)
                        ->company($company)
                        ->send();
                }
            }
        }
    }
}
