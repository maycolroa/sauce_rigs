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

class UpdateQualificationsRepeleadArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LegalMatrixTrait;

    protected $article;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($article)
    {
      $this->article = $article;
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

        $id_article = Article::find($this->article)->pluck('id')->toArray();

        $articles_qualification = DB::table('sau_lm_articles_fulfillment')
        ->where('article_id', $id_article)
        ->where('company_id', 1)
        //->limit(10)
        ->get();

        foreach ($articles_qualification as $key => $article2) 
        {
          
          DB::table('sau_lm_articles_fulfillment')->where('id', $article2->id)->update(
            [
              'fulfillment_value_id' => 8
            ]
          );

          $histories = ArticleFulfillmentHistory::create([
            'user_id' => 1,
            'fulfillment_value' => 'No vigente',
            'fulfillment_id' => $article2->id,
            'observations' => $article2->observations
          ]);
        }

        DB::commit();

      } catch (\Exception $e) {
          DB::rollback();
        \Log::info($e->getMessage());
      }
    }
}
