<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\LegalMatrix\Law;
use DB;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;
use App\Models\LegalAspects\LegalMatrix\Article;

class UpdateQualificationArticleRepelead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-qualification-article-repelead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion de calificacion de leyes que esten derogadas, se calificaran como no vigentes';

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
        try
        {
            DB::beginTransaction();
            
            $laws = Law::where('repealed', DB::raw("'SI'"))
            ->whereNull('company_id')
            ->limit(1)
            ->get();

            foreach ($laws as $key => $law) 
            {
                $ids_articles = Article::select('id')->where('law_id', $law->id)->pluck('id')->toArray();

                $articles_qualification = DB::table('sau_lm_articles_fulfillment')
                ->whereIn('article_id', $ids_articles)
                ->where('company_id', 1)
                //->limit(10)
                ->get();

                foreach ($articles_qualification as $key => $article) 
                {
                    DB::table('sau_lm_articles_fulfillment')
                    ->where('id', $article->id)
                    ->update(
                        [
                        'fulfillment_value_id' => 8
                        ]
                    );

                    $histories = ArticleFulfillmentHistory::create([
                        'user_id' => 1,
                        'fulfillment_value' => 'No vigente',
                        'fulfillment_id' => $article->id,
                        'observations' => $article->observations
                    ]);
                }       
            }  

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
        }
    }
}
