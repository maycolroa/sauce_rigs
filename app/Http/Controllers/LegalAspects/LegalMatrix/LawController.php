<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\CompanyIntetest;
use App\Jobs\LegalAspects\LegalMatrix\SyncQualificationsCompaniesJob;
use App\Traits\LegalMatrixTrait;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\LegalAspects\LegalMatrix\LawRequest;
use App\Http\Requests\LegalAspects\LegalMatrix\SaveArticlesQualificationRequest;
use Carbon\Carbon;
use Session;
use Validator;
use DB;

class LawController extends Controller
{
    use LegalMatrixTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('permission:activities_c', ['only' => 'store']);
        $this->middleware('permission:activities_r', ['except' =>'multiselect']);
        $this->middleware('permission:activities_u', ['only' => 'update']);
        $this->middleware('permission:activities_d', ['only' => 'destroy']);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        if ($request->has('qualify'))
        {
            $laws = Law::selectRaw(
                'sau_lm_laws.*,
                 IF(LENGTH(sau_lm_laws.description) > 50, CONCAT(SUBSTRING(sau_lm_laws.description, 1, 50), "..."), sau_lm_laws.description) AS description,
                 sau_lm_system_apply.name AS system_apply,
                 sau_lm_laws_types.name AS law_type,
                 sau_lm_risks_aspects.name AS risk_aspect,
                 sau_lm_entities.name AS entity,
                 sau_lm_sst_risks.name AS sst_risk,
                 SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) qualify,
                 SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) no_qualify'
            )
            ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
            ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
            ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
            ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
            ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
            ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
            ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
            ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
            ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
            ->where('sau_lm_articles_fulfillment.company_id', Session::get('company_id'))
            ->groupBy('sau_lm_laws.id');
        }
        else
        {
            $laws = Law::selectRaw(
                'sau_lm_laws.*,
                 IF(LENGTH(sau_lm_laws.description) > 50, CONCAT(SUBSTRING(sau_lm_laws.description, 1, 50), "..."), sau_lm_laws.description) AS description,
                 sau_lm_system_apply.name AS system_apply,
                 sau_lm_laws_types.name AS law_type,
                 sau_lm_risks_aspects.name AS risk_aspect,
                 sau_lm_entities.name AS entity,
                 sau_lm_sst_risks.name AS sst_risk'
            )
            ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
            ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
            ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
            ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
            ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id');

            if ($request->has('custom'))
                $laws->company();
            else
                $laws->system();
        }

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $laws->inLawTypes($this->getValuesForMultiselect($filters["lawTypes"]), $filters['filtersType']['lawTypes']);
            $laws->inRiskAspects($this->getValuesForMultiselect($filters["riskAspects"]), $filters['filtersType']['riskAspects']);
            $laws->inEntities($this->getValuesForMultiselect($filters["entities"]), $filters['filtersType']['entities']);
            $laws->inSstRisks($this->getValuesForMultiselect($filters["sstRisks"]), $filters['filtersType']['sstRisks']);
            $laws->inSystemApply($this->getValuesForMultiselect($filters["systemApply"]), $filters['filtersType']['systemApply']);
            $laws->inLawNumbers($this->getValuesForMultiselect($filters["lawNumbers"]), $filters['filtersType']['lawNumbers']);
            $laws->inLawYears($this->getValuesForMultiselect($filters["lawYears"]), $filters['filtersType']['lawYears']);
            $laws->inRepealed($this->getValuesForMultiselect($filters["repealed"]), $filters['filtersType']['repealed']);

            if ($request->has('qualify'))
            {
                $laws->inResponsibles($this->getValuesForMultiselect($filters["responsibles"]), $filters['filtersType']['responsibles']);
                $laws->inInterests($this->getValuesForMultiselect($filters["interests"]), $filters['filtersType']['interests']);
                $laws->inState($this->getValuesForMultiselect($filters["states"]), $filters['filtersType']['states']);
            }
        }

        return Vuetable::of($laws)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\LegalMatrix\LawRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LawRequest $request)
    {
        Validator::make($request->all(), [
            "file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $law = new Law($request->except('file'));
            
            if ($request->custom == 'true')
                $law->company_id = Session::get('company_id');

            if (!$law->save())
                return $this->respondHttp500();

            if ($request->file)
            {
                $file_tmp = $request->file;
                $nameFile = base64_encode(Auth::user()->id . now() . rand(1,10000)) .'.'. $file_tmp->extension();
                $file_tmp->storeAs('legalAspects/legalMatrix/', $nameFile, 's3');
                $law->file = $nameFile;

                if(!$law->update()){
                    return $this->respondHttp500();
                }
            }

            $this->saveArticles($law, $request->get('articles'));

            DB::commit();

            SyncQualificationsCompaniesJob::dispatch();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la norma'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $law = Law::findOrFail($id);
            $law->custom = $law->company_id ? true : false;  
            $law->old_file = $law->file;
            $law->multiselect_law_type = $law->lawType->multiselect();
            $law->multiselect_risk_aspect = $law->riskAspect->multiselect();
            $law->multiselect_entity = $law->entity->multiselect();
            $law->multiselect_sst_risk = $law->sstRisk->multiselect();
            $law->multiselect_system_apply = $law->systemApply->multiselect();
            $law->delete = [];

            foreach ($law->articles as $key => $article)
            {   
                $article->key = Carbon::now()->timestamp + rand(1,10000);
                $article->new_sequence = $article->sequence;
                $interests = [];

                foreach ($article->interests as $key => $interest)
                {
                    array_push($interests, $interest->multiselect());
                }

                $article->interests_id = $interests;
                $article->multiselect_interests = $interests;
            }

            return $this->respondHttp200([
                'data' => $law,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\LawRequest $request
     * @param  Law $law
     * @return \Illuminate\Http\Response
     */
    public function update(LawRequest $request, Law $law)
    {
        Validator::make($request->all(), [
            "file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $law->fill($request->except('file'));

            if ($request->file != $law->file)
            {
                $file = $request->file;
                Storage::disk('s3')->delete('legalAspects/legalMatrix/'. $law->file);
                $nameFile = base64_encode(Auth::user()->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs('legalAspects/legalMatrix/', $nameFile, 's3');
                $law->file = $nameFile;
            }

            if (!$law->update()) {
                return $this->respondHttp500();
            }

            $this->saveArticles($law, $request->get('articles'));
                
            if ($request->has('delete') && COUNT($request->delete) > 0)
            {
                foreach ($request->delete as $keyA => $article_id)
                {
                    $qualifications = ArticleFulfillment::withoutGlobalScopes()
                        ->where('article_id', $article_id)
                        ->get();

                    $this->deleteQualifications($qualifications);
                }

                Article::destroy($request->delete);
            }

            DB::commit();

            SyncQualificationsCompaniesJob::dispatch();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la norma'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Law $law
     * @return \Illuminate\Http\Response
     */
    public function destroy(Law $law)
    {
        $file = $law->file;

        foreach ($law->articles as $keyA => $article)
        {
            $qualifications = ArticleFulfillment::withoutGlobalScopes()
                ->where('article_id', $article->id)
                ->get();

            $this->deleteQualifications($qualifications);
        }

        if(!$law->delete())
            return $this->respondHttp500();

        Storage::disk('s3')->delete('legalAspects/legalMatrix/'. $file);
        
        return $this->respondHttp200([
            'message' => 'Se elimino la norma'
        ]);
    }

    public function lmYears()
    {
        $years = [];

        for ($i = 1901; $i <= Date('Y'); $i++)
        {     
            $years[$i] = $i;            
        }

        return $this->multiSelectFormat(collect($years));
    }

    public function download(Law $law)
    {
      return Storage::disk('s3')->download('legalAspects/legalMatrix/'. $law->file);
    }

    private function saveArticles($law, $articles)
    {
        foreach ($articles as $article)
        {
            $id = isset($article['id']) ? $article['id'] : NULL;
            $articleNew = $law->articles()->updateOrCreate(['id'=>$id], $article);
            $articleNew->interests()->sync($this->getValuesForMultiselect($article['interests_id']));
        }
    }

    public function lmLawYearsSystem(Request $request)
    {
        return $this->lmLawYears($request, 'system');
    }

    public function lmLawYearsCompany(Request $request)
    {
        return $this->lmLawYears($request, 'company');
    }

    public function lmLawYears(Request $request, $scope = 'alls')
    {
        $lawYears = Law::selectRaw(
            'DISTINCT(sau_lm_laws.law_year) as law_year'
        )
        ->$scope()
        ->pluck('law_year', 'law_year');
    
        return $this->multiSelectFormat($lawYears);
    }

    public function lawNumbersSystem(Request $request)
    {
        return $this->lmLawNumbers($request, 'system');
    }

    public function lawNumbersCompany(Request $request)
    {
        return $this->lmLawNumbers($request, 'company');
    }

    public function lmLawNumbers(Request $request, $scope = 'alls')
    {
        $lawNumbers = Law::selectRaw(
            'DISTINCT(sau_lm_laws.law_number) as law_number'
        )
        ->$scope()
        ->pluck('law_number', 'law_number');
    
        return $this->multiSelectFormat($lawNumbers);
    }

    public function lmLawResponsibles(Request $request)
    {
        $lawResponsibles = ArticleFulfillment::selectRaw(
            'DISTINCT(sau_lm_articles_fulfillment.responsible) as responsible'
        )
        ->whereNotNull('sau_lm_articles_fulfillment.responsible')
        ->pluck('responsible', 'responsible');
    
        return $this->multiSelectFormat($lawResponsibles);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function articlesQualificationsMultiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $values = FulfillmentValues::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($values)
            ]);
        }
        else
        {
            $values = FulfillmentValues::select(
                'sau_lm_fulfillment_values.id as id',
                'sau_lm_fulfillment_values.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($values);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getArticlesQualification(Law $law)
    {
        try
        {
            $law->lawType;
            $law->riskAspect;
            $law->entity;
            $law->sstRisk;
            $law->systemApply;

            $articles = Article::select('sau_lm_articles.*')
                ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
                ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
                ->groupBy('sau_lm_articles.id')
                ->where('sau_lm_articles.law_id', $law->id)
                ->orderBy('sau_lm_articles.sequence')
                ->get();

            $qualifications = ArticleFulfillment::all();
            $qualifications = $qualifications->groupBy('article_id')->toArray();

            $articles = $articles->filter(function ($article, $key) use ($qualifications) {
                return isset($qualifications[$article->id]);
            });

            $articles->transform(function($article, $index) use ($qualifications) {
                $article->key = Carbon::now()->timestamp + rand(1,10000);
                $interests = [];

                foreach ($article->interests as $key => $interest)
                {
                    array_push($interests, $interest->name);
                }

                $article->interests_list = $interests;
                $article->interests_string = implode(', ', $interests);

                $article->qualify = '';
                $article->qualification_id = $qualifications[$article->id][0]['id'];
                $article->fulfillment_value_id = $qualifications[$article->id][0]['fulfillment_value_id'];
                $article->observations = $qualifications[$article->id][0]['observations'];
                $article->file = $qualifications[$article->id][0]['file'];
                $article->old_file = $qualifications[$article->id][0]['file'];
                $article->responsible = $qualifications[$article->id][0]['responsible'];
                $article->qualify = $article->fulfillment_value_id ? FulfillmentValues::find($article->fulfillment_value_id)->name : '';
                $article->actionPlan = [
                    "activities" => [],
                    "activitiesRemoved" => []
                ];

                if ($article->qualify == 'No cumple')
                {
                    $article->actionPlan = ActionPlan::model(ArticleFulfillment::find($article->qualification_id))->prepareDataComponent();
                }

                return $article;
            });
            
            $law->articles = $articles;

            return $this->respondHttp200([
                'data' => $law,
            ]);

        } catch(Exception $e){
            return $this->respondHttp500();
        }
    }

    public function saveArticlesQualification(SaveArticlesQualificationRequest $request)
    {
        try
        {
            DB::beginTransaction();

            $data = $request->except('articles');

            $qualification = ArticleFulfillment::find($request->qualification_id);
            $qualification->fulfillment_value_id = $request->fulfillment_value_id ? $request->fulfillment_value_id : NULL;
            $qualification->observations = $request->observations ? $request->observations : NULL;
            $qualification->responsible = $request->responsible ? $request->responsible : NULL;

             //Se inician los atributos necesarios que seran estaticos para todas las actividades
            // De esta forma se evitar la asignacion innecesaria una y otra vez 
            ActionPlan::
                    user(Auth::user())
                ->module('legalMatrix')
                ->url(url('/administrative/actionplans'));

            if ($qualification->fulfillment_value_id)
            {
                $qualify = FulfillmentValues::find($qualification->fulfillment_value_id);

                if ($qualify->name != 'No cumple')
                {
                    if ($request->file != $qualification->file)
                    {
                        if ($request->file)
                        {
                            $file = $request->file;
                            Storage::disk('s3')->delete('legalAspects/legalMatrix/'. $qualification->file);
                            $nameFile = base64_encode(Auth::user()->id . now() . rand(1,10000)) .'.'. $file->extension();
                            $file->storeAs('legalAspects/legalMatrix/', $nameFile, 's3');
                            $qualification->file = $nameFile;
                            $data['file'] = $nameFile;
                            $data['old_file'] = $nameFile;
                        }
                        else
                        {
                            Storage::disk('s3')->delete('legalAspects/legalMatrix/'. $qualification->file);
                            $qualification->file = NUlL;
                            $data['file'] = NULL;
                            $data['old_file'] = NULL;
                        }
                    }
                }
                else
                {
                    if ($qualification->file)
                    {
                        Storage::disk('s3')->delete('legalAspects/legalMatrix/'. $qualification->file);
                        $qualification->file = NUlL;
                        $data['file'] = NULL;
                        $data['old_file'] = NULL;
                    }
                }

                /**Planes de acción*/
                ActionPlan::
                    model($qualification)
                    ->activities($request->actionPlan)
                    ->save();

                $data['actionPlan'] = ActionPlan::getActivities();
            }

            if (!$qualification->save())
                return $this->respondHttp500();

            ActionPlan::sendMail();

            DB::commit();

            return $this->respondHttp200([
                'data' => $data
            ]);

        } catch (Exception $e){
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function downloadArticleQualify(ArticleFulfillment $articleFulfillment)
    {
      return Storage::disk('s3')->download('legalAspects/legalMatrix/'. $articleFulfillment->file);
    }

    public function filterInterestsMultiselect(Request $request)
    {
        try
        {
            $articles = Article::select('sau_lm_articles.*')
                ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
                ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
                ->groupBy('sau_lm_articles.id')
                ->where('sau_lm_articles.law_id', $request->id)
                ->orderBy('sau_lm_articles.sequence')
                ->get();

            $qualifications = ArticleFulfillment::all();
            $qualifications = $qualifications->groupBy('article_id')->toArray();

            $articles = $articles->filter(function ($article, $key) use ($qualifications) {
                return isset($qualifications[$article->id]);
            });

            $company_interest = CompanyIntetest::pluck('interest_id', 'interest_id');

            $options = [];

            foreach ($articles as $key => $article)
            {
                foreach ($article->interests as $key => $interest)
                {
                    if (isset($company_interest[$interest->id]))
                        array_push($options, $interest->name);
                }
            }

            $options = array_unique($options);

            return $this->multiSelectFormat($options);

        } catch(Exception $e){
            return $this->respondHttp500();
        }
    }
}
