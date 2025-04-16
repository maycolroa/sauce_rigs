<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\IndustrialSecure\RoadSafety\Vehicle;
use App\Models\IndustrialSecure\RoadSafety\Maintenance;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\System\Licenses\License;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverDocument;
use Illuminate\Database\Eloquent\Builder;
use DB;

class NotifyExpiredDocumentsRoadSafety extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-expired-documents-road-safety';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de notificaciones de documentos proximos a vencerse ya sean del vehiculo o del conductor';

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
                        ->active();
                        //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');

            $users->company_scope = $company;
            $users = $users->get();

            $users = $users->filter(function ($user, $index) use ($company) {
                return $user->can('roadsafety_receive_notifications', $company) && !$user->isSuperAdmin($company);
            });

            $vehicles = Vehicle::select('*')
            ->whereRaw('(
                CURDATE() = DATE_ADD(due_date_soat, INTERVAL -7 DAY) 
                or CURDATE() = DATE_ADD(due_date_mechanical_tech, INTERVAL -7 DAY) 
                or CURDATE() = DATE_ADD(due_date_policy, INTERVAL -7 DAY)
             )');       
            $vehicles->company_scope = $company;
            //$vehicles = $vehicles->get();

            $vehicles_records = DB::table(DB::raw("({$this->getSqlWithBinding($vehicles)}) AS t"))
            ->select(
                'plate',
                DB::raw("CONCAT(
                    CASE WHEN CURDATE() = DATE_ADD(due_date_soat, INTERVAL -7 DAY) THEN 'SOAT. ' ELSE '' END,
                    CASE WHEN CURDATE() = DATE_ADD(due_date_mechanical_tech, INTERVAL -7 DAY) THEN 'Tecno mecánica. ' ELSE '' END,
                    CASE WHEN CURDATE() = DATE_ADD(due_date_policy, INTERVAL -7 DAY) THEN 'Poliza de responsabilidad civil.' ELSE '' END) AS message")
            );

        
            $vehicles_records = $vehicles_records->get();

            $table = [];

            if (COUNT($vehicles_records) > 0)
            {
                foreach ($vehicles_records as $value)
                {
                    array_push($table, [
                        'Placa o Conductor' => $value->plate,
                        'Documentos' => $value->message
                    ]);
                }
            }


            $drivers = Driver::select('sau_rs_drivers.*', 'sau_employees.name AS name', 'sau_employees.identification AS identification')
            ->join('sau_employees', 'sau_employees.id', 'sau_rs_drivers.employee_id');       
            $drivers->company_scope = $company;
            $drivers = $drivers->get();


            foreach ($drivers as $key => $driver) 
            {
                $documents = DriverDocument::select('*')
                ->where('driver_id', $driver->id)
                ->whereRaw('CURDATE() = DATE_ADD(sau_rs_drivers_documents.expiration_date, INTERVAL -7 DAY)')
                ->get();

                foreach ($documents as $key => $document) 
                {
                    array_push($table, [
                        'Placa o Conductor' => $driver->identification.' - '.$driver->name,
                        'Documentos' => $document->name
                    ]);
                }
            }

            if (COUNT($users) > 0)
            {
                foreach ($users as $key => $user) 
                {
                    if (COUNT($table) > 0)
                    {
                        NotificationMail::
                            subject('Seguridad Vial: Documentos proximos a vencerse')
                            ->view('notification')
                            ->recipients($user)
                            ->message('Listado de documentos que estan a 7 dias de vencerse')
                            ->module('roadSafety')
                            ->event('Tarea programada: NotifyExpiredDocumentsRoadSafety')
                            ->table($table)
                            ->company($company)
                            ->send();
                    }
                }
            }
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
