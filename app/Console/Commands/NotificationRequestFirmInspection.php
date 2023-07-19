<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionFirm;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;
use DB;

class NotificationRequestFirmInspection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification-request-firm-inspection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificacion de la solicitud de firma a un usuario en inspecciones planeadas';

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
            ->where('sau_license_module.module_id', '26');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $users_request_firms = collect([]);

            $request_firms = InspectionFirm::select(
                'sau_ph_qualification_inspection_firm.user_id AS user_id',
                DB::raw('sau_users.email AS email'),
                DB::raw('COUNT(sau_ph_qualification_inspection_firm.id) AS total'),
                DB::raw('MAX(sau_companies.name) AS company')
            )
            ->join('sau_companies', 'sau_companies.id', 'sau_ph_qualification_inspection_firm.company_id')
            ->join('sau_users', 'sau_users.id', 'sau_ph_qualification_inspection_firm.user_id')
            ->where('sau_ph_qualification_inspection_firm.state', 'Pendiente')
            ->where('company_id', $company)
            ->where('sau_users.active', 'SI')
            ->groupBy('user_id')
            ->get();

            foreach ($request_firms as $key => $firms) 
            {
                $recipient = new User(["email" => $firms->email]); 

                NotificationMail::
                    subject('Inspecciones - Inspecciones planeadas pendientes por firmar')
                    ->recipients($recipient)
                    ->message("Estimado usuario usted tiene <b>$firms->total</b> inspecciones planeadas pendientes por firmar en la compañia <b>$firms->company</b> ")
                    ->module('dangerousConditions')
                    ->event('Tarea programada: NotificationRequestFirmInspection')
                    ->buttons([
                        ['text'=>'Llevarme al sitio', 'url'=> url('/industrialsecure/dangerousconditions/inspections/request/firm')]
                    ])
                    ->company($company)
                    ->send();
            }
        }
    }
}
