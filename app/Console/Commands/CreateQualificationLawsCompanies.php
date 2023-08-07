<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\System\Licenses\License;
use App\Traits\LegalMatrixTrait;

class CreateQualificationLawsCompanies extends Command
{
    use LegalMatrixTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-qualification-laws-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear calificaciones de articulos de las leyes propias de cada compañia sin importar si estas cumplen con sus intereses o no';

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
        $companies = License::selectRaw('DISTINCT company_id AS id, sau_companies.name AS name')
        ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
        ->join('sau_companies', 'sau_companies.id', 'sau_licenses.company_id')
        ->withoutGlobalScopes()
        ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
        ->where('sau_license_module.module_id', '17')
        ->get();

        foreach ($companies as $key => $company) 
        {
            $laws = Law::select('*')
            ->withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->get();

            foreach ($laws as $key => $law) 
            {
                $this->syncQualificationsCompanies($law->id);
            }
        }
    }
}
