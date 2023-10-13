<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategory;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItems;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryCompany;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItemsCompany;
use App\Models\System\Licenses\License;
use App\Models\General\Company;

class AccidentCategoryItemDefaultValueCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accident-category-item-default-value-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creacion de valores por defecto para las categorias e items de causas de accidentes para las licencias de inspecciones';

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
        $categoriesDefault = SectionCategory::get();

        $companies = License::selectRaw('DISTINCT company_id')
        ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
        ->withoutGlobalScopes()
        ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
        ->where('sau_license_module.module_id', '26');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            foreach ($categoriesDefault as $keyC => $value) 
            {
                $category = SectionCategoryCompany::firstOrCreate(
                    [
                        'category_name' => $value->category_name,
                        'company_id' => $company,
                        'section_id' => $value->section_id,
                    ], 
                    [
                        'category_name' => $value->category_name, 
                        'company_id' => $company,
                        'section_id' => $value->section_id,
                        'category_default_id' => $value->id
                    ]
                );

                $itemsDefault = SectionCategoryItems::where('category_id', $value->id)->get();

                foreach ($itemsDefault as $keyI => $item) 
                {
                    $itemCompany = SectionCategoryItemsCompany::firstOrCreate(
                        [
                            'item_name' => $item->item_name,
                            'company_id' => $company,                            
                            'category_id' => $category->id,
                        ], 
                        [
                            'item_name' => $item->item_name, 
                            'company_id' => $company,
                            'category_id' => $category->id,
                        ]
                    );
                }
            }
        }
    }
}
