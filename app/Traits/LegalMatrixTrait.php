<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Facades\ActionPlans\Facades\ActionPlan;
use DB;

trait LegalMatrixTrait
{
    public function getArticlesCompany($company_id = null)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $articles = Article::select('sau_lm_articles.*')
            ->join('sau_lm_laws', 'sau_lm_laws.id', 'sau_lm_articles.law_id')
            ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
            ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
            ->alls($company_id)
            ->groupBy('sau_lm_articles.id');

        if ($company_id)
            $articles->company_scope = $company_id;

        return $articles->get();
    }

    public function syncQualificationsCompany($company_id)
    {
        if ($company_id && !is_numeric($company_id))
            throw new \Exception('Company invalid');

        $articles = $this->getArticlesCompany($company_id);
        $ids_article = [];

        foreach ($articles as $key => $value)
        {
            $qualification = ArticleFulfillment::query();
            $qualification->company_scope = $company_id;

            $qualification = $qualification->firstOrCreate(
                ['article_id' => $value->id],
                ['article_id' => $value->id, 'company_id' => $company_id]
            );

            array_push($ids_article, $value->id);
        }

        /*if (COUNT($ids_article) > 0)
        {*/
            /*$articlesDelete = ArticleFulfillment::whereNotIn('article_id', $ids_article);
            $articlesDelete->company_scope = $company_id;
            $articlesDelete = $articlesDelete->get();

            $this->deleteQualifications($articlesDelete);*/
            $this->deleteQualificationsDuplicates($company_id);
        //}
    }

    public function deleteQualifications($qualifications)
    {
        if (!$qualifications instanceof Collection)
            throw new \Exception('Qualifications format invalid');

        foreach ($qualifications as $keyQ => $qualification)
        {
            if ($qualification->fulfillment_value_id)
            {
                $qualify = FulfillmentValues::find($qualification->fulfillment_value_id);

                if ($qualify->name != 'No cumple' && $qualify->name != 'Parcial')
                {
                    $path = "fulfillments/".$qualification->company_id."/";
                    Storage::disk('s3_MLegal')->delete($path.$qualification->file);
                }
                else
                {
                    ActionPlan::company($qualification->company_id)->model($qualification)->modelDeleteAll();
                }
            }
            
            $qualification->delete();
        }
    }

    private function deleteQualificationsDuplicates($company_id)
    {
        $articlesDelete = ArticleFulfillment::selectRaw('GROUP_CONCAT(id) AS data')
            ->groupBy('article_id')
            ->havingRaw('COUNT(id) > 1');

        $articlesDelete->company_scope = $company_id;
        $articlesDelete = $articlesDelete->get();

        $ids = [];

        foreach ($articlesDelete as $key => $value)
        {
            $aux = explode(",", $value->data, 2);
            array_push($ids, $aux[1]);
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));
            $articlesDelete = ArticleFulfillment::whereIn('id', $ids);
            $articlesDelete->company_scope = $company_id;
            $articlesDelete = $articlesDelete->get();
            
            $this->deleteQualifications($articlesDelete);
        }
    }

    public function syncQualificationsCompanies()
    {
        $companies = DB::table('sau_lm_company_interest')
            ->selectRaw('DISTINCT company_id AS company_id')
            ->get();

        foreach ($companies as $key => $value)
        {
            \Log::info("sincronizando intereses de la compañia {$value->company_id}");
            
            $this->syncQualificationsCompany($value->company_id);
        }
    }
}