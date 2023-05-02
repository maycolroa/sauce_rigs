<?php

namespace App\Jobs\LegalAspects\LegalMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\LegalMatrix\Interest;
use App\Traits\LegalMatrixTrait;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;
use App\Models\LegalAspects\LegalMatrix\Article;
use DB;

class UpdateQualificationsRepelead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    protected $law;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($law)
    {
      $this->law = $law;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      try
      {
        DB::beginTransaction();

        $ids_articles = Article::select('id')->where('law_id', $this->law->id)->pluck('id')->toArray();

        $articles_qualification = DB::table('sau_lm_articles_fulfillment')
        ->whereIn('article_id', $ids_articles)
        ->where('company_id', 1)
        //->limit(10)
        ->get();

        foreach ($articles_qualification as $key => $article) 
        {
          DB::table('sau_lm_articles_fulfillment')->where('id', $article->id)->update(
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

        DB::commit();

      } catch (\Exception $e) {
          DB::rollback();
        \Log::info($e->getMessage());
      }
    }
}
