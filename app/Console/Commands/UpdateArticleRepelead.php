<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\LegalMatrix\Law;
use DB;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;
use App\Models\LegalAspects\LegalMatrix\Article;

class UpdateArticleRepelead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-article-repelead';

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
                $articles = Article::where('law_id', $law->id)->get();

                foreach ($articles as $key => $article) 
                {
                    DB::table('sau_lm_articles')
                    ->where('id', $article->id)
                    ->update(
                        [
                        'repealed' => 'SI'
                        ]
                    );

                    $article->histories()->create([
                        'user_id' => 1
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
