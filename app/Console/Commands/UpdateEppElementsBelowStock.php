<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementStockMinimun;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\System\Licenses\License;
use App\Models\General\Company;

class UpdateEppElementsBelowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-epp-elements-below-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar campo para determinar si un elemento tiene el stock por debajo de su existencia minima configurada';

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
                        $stock->below_stock = true;
                        $stock->save();
                    }
                }
            }
        }
    }
}
