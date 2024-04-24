<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\LegalAspects\Contracts\TagsArl;

class UploadInformationTagArlContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-information-tag-arl-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creacion de tags de ARL por defecto para las empresas que tengan el modulo de conntratistas';

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
        $arls =  [
            'POSITIVA',
            'ARL COLMENA',
            'AXA COLPATRIA',
            'LA EQUIDAD',
            'ARL SURA',
            'SEGUROS BOLIVAR',
        ];

        $companies = License::selectRaw('DISTINCT company_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->withoutGlobalScopes()
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', '16');

        $companies = $companies->pluck('sau_licenses.company_id');

        foreach ($companies as $key => $company)
        {
            foreach ($arls as $key => $arl) {
                TagsArl::firstOrCreate(
                    [
                        'name' => $arl,
                        'company_id' => $company
                    ],
                    [
                        'name' => $arl,
                        'company_id' => $company
                    ]
                    );
            }
        }
    }
}
