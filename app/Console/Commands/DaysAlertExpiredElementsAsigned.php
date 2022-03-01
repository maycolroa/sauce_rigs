<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Models\IndustrialSecure\Epp\Location;
use App\Models\Administrative\Employees\Employee;
use Carbon\Carbon;
use DB;

class DaysAlertExpiredElementsAsigned extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'days-alert-expired-elements-asigned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alerta configurable para la notificacion de elementos asignados proximos a vencerse';

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
            ->where('sau_license_module.module_id', '34'); //32 prod

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            $expired_elements = [];
            
            $companyElement = Company::find($company);

            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;

            $elements_expired = Element::where('expiration_date', true);
            $elements_expired->company_scope = $companyElement->id;
            $elements_expired = $elements_expired->get();

            foreach ($elements_expired as $key => $ele) 
            {
                $elements_ubication = ElementBalanceLocation::where('element_id', $ele->id)->get();

                foreach ($elements_ubication as $key => $ele_ubc) 
                {
                    $elements_asigned = ElementBalanceSpecific::where('element_balance_id', $ele_ubc->id)->where('state', 'Asignado')->get();

                    foreach ($elements_asigned as $key => $ele_asi) 
                    {                        
                        $deliveries = ElementTransactionEmployee::join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
                        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
                        ->where('sau_epp_elements_balance_specific.id', $ele_asi->id)
                        ->where('sau_epp_transactions_employees.type', 'Entrega')
                        ->whereNull('sau_epp_transactions_employees.state');

                        $deliveries->company_scope = $companyElement->id;
                        $deliveries = $deliveries->get();

                        foreach ($deliveries as $key => $delivery) 
                        {
                            $employee = Employee::where('id', $delivery->employee_id);
                            $employee->company_scope = $companyElement->id;
                            $employee = $employee->first();

                            $location = Location::where('id', $delivery->location_id);
                            $location->company_scope = $companyElement->id;
                            $location = $location->first();

                            $dateUpload = Carbon::createFromFormat('Y-m-d H:i:s', $delivery->created_at);

                            if ($dateUpload->diffInDays(Carbon::now()) >= $configDay)
                            {
                                $content = [
                                    'Empleado' => $employee->name,
                                    'Elemento' => $ele->name,
                                    'Código' => $ele_asi->hash,
                                    'Ubicación' => $location->name
                                ];

                                array_push($expired_elements, $content);
                            }
                        }
                    }
                }
            }

            if (count($expired_elements) > 0)
            {
                $responsibles = ConfigurationsCompany::company($company)->findByKey('users_notify_element_expired');

                if ($responsibles)
                    $responsibles = explode(',', $responsibles);

                if (count($responsibles) > 0)
                {
                    foreach ($responsibles as $email)
                    {
                        $recipient = new User(["email" => $email]); 

                        NotificationMail::
                            subject('Sauce - EPP ELementos asignados próximos a vencerse')
                            ->recipients($recipient)
                            ->message("Este es el listado de elementos asignados que estan a <b>$configDay</b> dias de vencerse")
                            ->module('epp')
                            ->event('Tarea programada: DaysAlertExpiredElementsAsigned')
                            ->table($expired_elements)
                            ->company($company)
                            ->send();
                    }
                }
            }
        }
    }

    public function getConfig($company_id)
    {
        $key = "expired_elements_asigned";
        $key1 = "days_alert_expiration_date_elements";

        $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

        if ($exists && $exists == 'SI')
        {
            $days = ConfigurationsCompany::company($company_id)->findByKey($key1);
            return $days;
        }
        else
            return NULL;

    }
}
