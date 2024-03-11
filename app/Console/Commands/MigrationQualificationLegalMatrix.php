<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\General\Company;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;
use App\Models\LegalAspects\LegalMatrix\CompanyIntetest;

class MigrationQualificationLegalMatrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration-qualification-legal-matrix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar informacion de matriz legal de una compañia a otra, incluido intereses y calificaciones';

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
        /// Guardado de intereses///
        /*$company_origin = Company::find(691);

        $company_destiny = Company::find(710);

        $company_destiny->interests()->sync($company_origin->interests()->pluck('id'));

        ///Calificaciones///
        $qualifications_origin = ArticleFulfillment::withoutGlobalScopes()->where('company_id', $company_origin->id)->whereNotIn('article_id', [20568,20569,20570])->get();

        if ($qualifications_origin)
        {
            foreach ($qualifications_origin as $key => $value) 
            {
                $qualification = ArticleFulfillment::query();
                $qualification->company_scope = $company_destiny->id;
                $qualification = $qualification->firstOrCreate(
                    [
                        'article_id' => $value->article_id, 
                    ],
                    [
                        'company_id' => $company_destiny->id,
                        'article_id' => $value->article_id, 
                        'fulfillment_value_id' => $value->fulfillment_value_id,
                        'observations' => $value->observations,
                        'file' => $value->file,
                        'responsible' => $value->responsible,
                        'workplace' => $value->workplace,
                        'hide' => $value->hide,
                        'created_at' => $value->created_at,
                        'updated_at' => $value->updated_at,
                        'qualification_masive' => $value->qualification_masive,
                        'date_qualification_edit' => $value->date_qualification_edit
                    ]
                );
            }
        }*/
    }
}
