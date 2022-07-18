<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementStockMinimun;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\Location;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;

class NotifyAlertStockMinimun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-alert-stock-minimun';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificacion a usuarios autorizado sobre la existencia minima en una ubicacion especifica';

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
        ->where('sau_license_module.module_id', '32' /*32 prod, 34 local*/);

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {            
            $companyElement = Company::find($company);

            $configDay = $this->getConfig($company);

            if (!$configDay)
                continue;

            $element_notify = [];
            $elements = Element::where('stock_minimun', 1);
            $elements->company_scope = $company;
            $elements = $elements->get();

            foreach ($elements as $key => $element) 
            {
                $stocks_minimos = ElementStockMinimun::where('element_id', $element->id)->get();

                foreach ($stocks_minimos as $key => $stock) 
                {
                    $disponibles = ElementBalanceLocation::selectRaw("
                        SUM(IF(sau_epp_elements_balance_specific.state = 'Disponible', 1, 0)) AS disponibles
                    ")
                    ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                    ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_elements_balance_ubication.location_id')
                    ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.element_balance_id', 'sau_epp_elements_balance_ubication.id')
                    ->where('sau_epp_elements_balance_ubication.element_id', $element->id)
                    ->where('sau_epp_elements_balance_ubication.location_id', $stock->location_id)
                    ->first();

                    if ($disponibles->disponibles && $disponibles->disponibles < $stock->quantity)
                    {
                        $location = Location::withoutGlobalScopes()->find($stock->location_id);

                        $content = [
                            'Elemento' => $element->name,
                            'Clase' => $element->class_element,
                            'Marca' => $element->mark,
                            'Ubicación' => $location->name,
                            'Existencia Mínima' => $stock->quantity,
                            'Disponible' => $disponibles->disponibles
                        ];

                        array_push($element_notify, $content);
                    }
                }
            }

            if (count($element_notify) > 0)
            {
                $responsibles = ConfigurationsCompany::company($company)->findByKey('users_notify_stock_minimun');

                if ($responsibles)
                    $responsibles = explode(',', $responsibles);

                if (count($responsibles) > 0)
                {
                    foreach ($responsibles as $email)
                    {
                        $recipient = new User(["email" => $email]); 

                        NotificationMail::
                            subject('Sauce - EPP Elementos Existencia mínima')
                            ->recipients($recipient)
                            ->message("Este es el listado de elementos cuya existencia disponibles esta por debajo de la existencia mínima configurada")
                            ->module('epp')
                            ->event('Tarea programada: NotifyAlertStockMinimun')
                            ->table($element_notify)
                            ->company($company)
                            ->send();
                    }
                }
            }
        }
    }

    public function getConfig($company_id)
    {
        $key = "stock_minimun";

        $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

        if ($exists && $exists == 'SI')
            return true;
        else
            return false;

    }
}
