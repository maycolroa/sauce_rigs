<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;
use App\Models\LegalAspects\LegalMatrix\CompanyIntetest;
use App\Models\LegalAspects\LegalMatrix\LawHide;
use App\Jobs\LegalAspects\LegalMatrix\SyncQualificationsCompaniesJob;
use App\Jobs\LegalAspects\LegalMatrix\UpdateQualificationsRepeleadArticle;
use App\Traits\LegalMatrixTrait;
use App\Traits\Filtertrait;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\LegalAspects\LegalMatrix\LawRequest;
use App\Http\Requests\LegalAspects\LegalMatrix\SaveArticlesQualificationRequest;
use App\Exports\LegalAspects\LegalMatrix\Laws\LegalMatrixImport;
use App\Jobs\LegalAspects\LegalMatrix\LawImportJob;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;

class LawController extends Controller
{
    use LegalMatrixTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:laws_c|lawsCustom_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:laws_r|lawsCustom_r, {$this->team}", ['except' => 'getArticlesQualification']);
        $this->middleware("permission:laws_u|lawsCustom_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:laws_d|lawsCustom_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:laws_qualify, {$this->team}", ['only' => 'saveArticlesQualification']);
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
            $laws_hides = LawHide::select('law_id')->get()->toArray();

            $hides = [];

            foreach ($laws_hides as $key => $value) 
            {
                array_push($hides, $value['law_id']);
            }

            $laws = Law::selectRaw(
                'sau_lm_laws.*,
                 IF(LENGTH(sau_lm_laws.description) > 50, CONCAT(SUBSTRING(sau_lm_laws.description, 1, 50), "..."), sau_lm_laws.description) AS description,
                 sau_lm_system_apply.name AS system_apply,
                 sau_lm_laws_types.name AS law_type,
                 sau_lm_risks_aspects.name AS risk_aspect,
                 sau_lm_entities.name AS entity,
                 sau_lm_sst_risks.name AS sst_risk,
                 IF(sau_lm_laws_hide_companies.id IS NOT NULL, "SI", "NO") hide,
                 SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NOT NULL, 1, 0)) qualify,
                 SUM(IF(sau_lm_articles_fulfillment.fulfillment_value_id IS NULL, 1, 0)) no_qualify'
            )
            ->withoutGlobalScopes()
            ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
            ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
            ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
            ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
            ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
            ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
            ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
            ->leftJoin('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
            ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
            ->leftJoin('sau_lm_laws_hide_companies', 'sau_lm_laws_hide_companies.law_id', 'sau_lm_laws.id')
            ->leftJoin('sau_companies', 'sau_companies.id', 'sau_lm_laws.company_id')
            //->where('sau_lm_articles_fulfillment.company_id', $this->company);
            ->whereRaw("((sau_lm_articles_fulfillment.company_id = {$this->company} and sau_lm_company_interest.company_id = {$this->company}) or (sau_lm_articles_fulfillment.company_id = {$this->company} and sau_lm_laws.company_id = {$this->company}))");

            if (!$this->user->hasRole('Superadmin', $this->company) && COUNT($hides) > 0)
                $laws->whereNotIn('sau_lm_laws.id', $hides);
            
            $laws->groupBy('sau_lm_laws.id', 'sau_lm_laws_hide_companies.id');

            $url = "/legalaspects/lm/lawsQualify";
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
            {
                $laws->company();                
                $url = "/legalaspects/lm/lawsCompany";
            }
            else
            {                
                $laws->system();
                $url = "/legalaspects/lm/laws";
            }            
        }

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

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

            if ($request->has('qualify') || $url == '/legalaspects/lm/lawsQualify')
            {
                $laws->inResponsibles($this->getValuesForMultiselect($filters["responsibles"]), $filters['filtersType']['responsibles']);
                $laws->inInterests($this->getValuesForMultiselect($filters["interests"]), $filters['filtersType']['interests']);
                $laws->inState($this->getValuesForMultiselect($filters["states"]), $filters['filtersType']['states']);

                if (isset($filters["qualifications"]) && COUNT($filters["qualifications"]) > 0)
                    $laws->inQualification($this->getValuesForMultiselect($filters["qualifications"]), $filters['filtersType']['qualifications']);
                
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
            $data = $request->all();
            
            $law = new Law($request->except('file'));
            
            if ($request->custom == 'true')
                $law->company_id = $this->company;

            if (!$law->save())
                return $this->respondHttp500();

            if ($request->file)
            {
                $path = ($request->custom != 'true') ? 'laws/' : "laws/".$this->company."/";
                $file_tmp = $request->file;
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->extension();
                $file_tmp->storeAs($path, $nameFile, 's3_MLegal');
                $law->file = $nameFile;

                if(!$law->update()){
                    return $this->respondHttp500();
                }

                $data['file'] = $law->file;
                $data['old_file'] = $law->file;
            }

            $data['id'] = $law->id;
            $data['articles'] = $this->saveArticles($law, $request->get('articles'));
            $data['delete'] = [];

            $this->saveLogActivitySystem('Matriz legal - Normas', 'Se creo la norma '.$law->name);

            DB::commit();

            SyncQualificationsCompaniesJob::dispatch($law->id);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

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
                $article->key = (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20);
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

            $data = $request->except(['multiselect_law_type', 'multiselect_risk_aspect', 'multiselect_entity', 'multiselect_sst_risk', 'multiselect_system_apply']);

            if ($request->file != $law->file)
            {
                $path = (!$law->company_id) ? 'laws/' : "laws/".$this->company."/";
                $file = $request->file;
                Storage::disk('s3_MLegal')->delete($path.$law->file);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs($path, $nameFile, 's3_MLegal');
                $law->file = $nameFile;

                $data['file'] = $law->file;
                $data['old_file'] = $law->file;
            }

            if (!$law->update()) {
                return $this->respondHttp500();
            }

            $data['articles'] = $this->saveArticles($law, $request->get('articles'));
            $data['delete'] = [];
                
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

            $this->saveLogActivitySystem('Matriz legal - Normas', 'Se edito la norma '.$law->name);

            DB::commit();

            SyncQualificationsCompaniesJob::dispatch($law->id);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

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

        $this->saveLogActivitySystem('Matriz legal - Normas', 'Se elimino la norma '.$law->name);

        if(!$law->delete())
            return $this->respondHttp500();

        $path = (!$law->company_id) ? 'laws/' : "laws/".$this->company."/";

        Storage::disk('s3_MLegal')->delete($path.$file);
        
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

        arsort($years);

        return $this->multiSelectFormat(collect($years));
    }

    public function download(Law $law)
    {
        $path = (!$law->company_id) ? 'laws/' : "laws/".$this->company."/";
        return Storage::disk('s3_MLegal')->download($path.$law->file);
    }

    public function showFile(Law $law)
    {
        $path = (!$law->company_id) ? 'laws/' : "laws/".$this->company."/";
        return Storage::disk('s3_MLegal')->url($path.$law->file);
    }

    private function saveArticles($law, $articles)
    {
        foreach ($articles as $key => $article)
        {
            if ($law->repealed == 'SI')
                $article['repealed'] = 'SI';
                
            $id = isset($article['id']) ? $article['id'] : NULL;
            $articleNew = $law->articles()->updateOrCreate(['id'=>$id], $article);
            $articleNew->interests()->sync($this->getValuesForMultiselect($article['interests_id']));
            $articles[$key]['id'] = $articleNew->id;

            if ($law->repealed == 'Parcial')
            {
                if ($articleNew->repealed == 'SI')
                    UpdateQualificationsRepeleadArticle::dispatch($articleNew->id);
            }
        }

        return $articles;
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
            if ($law->file)
            {
                $path = (!$law->company_id) ? 'laws/'.$law->file : "laws/".$this->company."/".$law->file;
                $law->url = Storage::disk('s3_MLegal')->url($path);
            }
            else
                $law->url = '';
            
            $law->lawType;
            $law->riskAspect;
            $law->entity;
            $law->sstRisk;
            $law->systemApply;

            if ($law->company_id)
            {
                $articles = Article::select('sau_lm_articles.*')
                ->groupBy('sau_lm_articles.id')
                ->where('sau_lm_articles.law_id', $law->id)
                ->orderBy('sau_lm_articles.sequence')
                ->get();
            }
            else
            {            
                $articles = Article::select('sau_lm_articles.*')
                ->join('sau_lm_article_interest', function ($join) use ($law)
                {
                    $join->on("sau_lm_article_interest.article_id", "sau_lm_articles.id");
                    $join->on("sau_lm_articles.law_id", DB::raw($law->id));
                })
                ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
                ->groupBy('sau_lm_articles.id')
                //->where('sau_lm_articles.law_id', $law->id)
                ->orderBy('sau_lm_articles.sequence')
                ->get();
            }

            $qualifications = ArticleFulfillment::all();
            $qualifications = $qualifications->groupBy('article_id')->toArray();

            $articles = $articles->filter(function ($article, $key) use ($qualifications) {
                return isset($qualifications[$article->id]);
            });

            $articles->transform(function($article, $index) use ($qualifications) {
                $article->key = (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20);
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
                $article->workplace = $qualifications[$article->id][0]['workplace'];
                $article->hide = $qualifications[$article->id][0]['hide'];
                $article->qualify = $article->fulfillment_value_id ? FulfillmentValues::find($article->fulfillment_value_id)->name : '';
                $article->actionPlan = [
                    "activities" => [],
                    "activitiesRemoved" => []
                ];

                if ($article->qualify == 'No cumple' || $article->qualify == 'Parcial')
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

    public function saveArticlesQualificationAlls(SaveArticlesQualificationRequest $request)
    {     
        DB::beginTransaction();

        try
        {           
            $ids = explode(',', $request->id);

            $article_qualify = ArticleFulfillment::find($ids[0]);
            $article_law = Article::find($article_qualify->article_id);
            $law = Law::find($article_law->law_id);

            if ($request->fulfillment_value_id == 8 && $law->company_id == NULL)
            {
                return $this->respondWithError("Está norma es de Sauce no puede calificarse como 'No vigente'");
            }
            else
            {
                $path = 'fulfillments/'.$this->company."/";

                $nameFile = NULL;

                $qualification = ArticleFulfillment::whereIn('id', $ids)
                ->update([
                    'fulfillment_value_id' => $request->fulfillment_value_id ? $request->fulfillment_value_id : NULL,
                    'observations' => $request->observations ? $request->observations : NULL,
                    'responsible' => $request->responsible ? $request->responsible : NULL,
                    'workplace' => $request->workplace ? $request->workplace : NULL,
                    'qualification_masive' => true,
                    'hide' => $request->hide ? $request->hide : 'NO'
                ]);

                $article_qualify = ArticleFulfillment::find($ids[0]);
                $article_law = Article::find($article_qualify->article_id);
                $law = Law::find($article_law->law_id);

                if ($request->hide == 'SI')
                {
                    $law_hide = LawHide::updateOrCreate(['company_id' => $this->company, 'law_id' => $law->id], ['company_id' => $this->company, 'law_id' => $law->id, 'user_id' => $this->user->id]);
                }
                else
                {
                    $law_hide = LawHide::where('company_id', $this->company)
                    ->where('law_id', $law->id)
                    ->first();

                    if ($law_hide)
                        $law_hide->delete();
                }

                if ($request->file)
                {
                    $file = $request->file;
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                    $file->storeAs($path, $nameFile, 's3_MLegal');
                    $data['file'] = $nameFile;
                    $data['old_file'] = $nameFile;

                    $qualification_file = ArticleFulfillment::whereIn('id', $ids)
                    ->update([
                        'file' => $nameFile
                    ]);
                }
                
                if ($request->has('actionPlan') && count($request->actionPlan['activities']) > 0)
                {
                    $detail_procedence = 'Mátriz Legal - Norma: ' . $article_qualify->article->law->name . '. - ' . 'Artículo: ' . $article_qualify->article->description . '.';

                    ActionPlan::user($this->user)
                    ->module('legalMatrix')
                    ->url(url('/administrative/actionplans'))
                    ->model($article_qualify)
                    ->detailProcedence($detail_procedence)
                    ->activities($request->actionPlan)
                    ->save();
                }

                foreach ($ids as $id) 
                {
                    $article = ArticleFulfillment::find($id);

                    $article->histories()->create([
                        'user_id' => $this->user->id,
                        'fulfillment_value' => $article->fulfillment_value ? $article->fulfillment_value->name : NULL,
                        'observations' => $article->observations
                    ]);
                }

                if (!$qualification) {
                    return $this->respondHttp500();
                }

                DB::commit();
            }

        } catch (Exception $e){
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function saveArticlesQualification(SaveArticlesQualificationRequest $request)
    {
        try
        {
            $data = $request->except('articles');


            $qualification = ArticleFulfillment::find($request->qualification_id);

            if ($request->fulfillment_value_id == 8 && !$qualification->article->law->company_id && $qualification->fulfillment_value_id != 8)
            {
                return $this->respondWithError("Está norma es de Sauce no puede calificarse como 'No vigente'");
            }
            else
            {
                DB::transaction(function () use (&$data, $request, $qualification) {

                    $qualification->fulfillment_value_id = $request->fulfillment_value_id ? $request->fulfillment_value_id : NULL;
                    $qualification->observations = $request->observations ? $request->observations : NULL;
                    $qualification->responsible = $request->responsible ? $request->responsible : NULL;
                    $qualification->workplace = $request->workplace ? $request->workplace : NULL;
                    $qualification->hide = $request->hide ? $request->hide : 'NO';

                    if ($request->hide == 'NO')
                    {
                        $law_hide = LawHide::where('company_id', $this->company)
                        ->where('law_id', $qualification->article->law->id)
                        ->first();

                        if ($law_hide)
                            $law_hide->delete();
                    }

                    //Se inician los atributos necesarios que seran estaticos para todas las actividades
                    // De esta forma se evitar la asignacion innecesaria una y otra vez 
                    ActionPlan::
                            user($this->user)
                        ->module('legalMatrix')
                        ->url(url('/administrative/actionplans'));

                    $path = 'fulfillments/'.$this->company."/";

                    if ($qualification->fulfillment_value_id)
                    {
                        $qualify = FulfillmentValues::find($qualification->fulfillment_value_id);

                        if ($qualify->name != 'No cumple' && $qualify->name != 'Parcial')
                        {
                            if ($request->file != $qualification->file)
                            {
                                if ($request->file)
                                {
                                    $file = $request->file;

                                    if (!$qualification->qualification_masive)
                                        Storage::disk('s3_MLegal')->delete($path. $qualification->file);

                                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                                    $file->storeAs($path, $nameFile, 's3_MLegal');
                                    $qualification->file = $nameFile;
                                    $data['file'] = $nameFile;
                                    $data['old_file'] = $nameFile;
                                }
                                else
                                {
                                    if (!$qualification->qualification_masive)
                                        Storage::disk('s3_MLegal')->delete($path. $qualification->file);
                                    
                                    $qualification->file = NUlL;
                                    $data['file'] = NULL;
                                    $data['old_file'] = NULL;
                                }
                            }
                        }
                        else
                        {
                            /*if ($qualification->file)
                            {
                                Storage::disk('s3_MLegal')->delete($path. $qualification->file);
                                $qualification->file = NUlL;
                                $data['file'] = NULL;
                                $data['old_file'] = NULL;
                            }*/
                        }

                        $detail_procedence = 'Mátriz Legal - Norma: ' . $qualification->article->law->name . '. - ' . 'Artículo: ' . $qualification->article->description . '.';

                        /**Planes de acción*/
                        ActionPlan::
                            model($qualification)
                            ->detailProcedence($detail_procedence)
                            ->activities($request->actionPlan)
                            ->save();

                        $data['actionPlan'] = ActionPlan::getActivities();
                    }

                    if (!$qualification->save())
                        return $this->respondHttp500();

                    ActionPlan::sendMail();

                    $data['fulfillment_value_id'] = (int) $qualification->fulfillment_value_id;

                }, 3);

                return $this->respondHttp200([
                    'data' => $data
                ]);
            }

        } catch (Exception $e){
            return $this->respondHttp500();
        }
    }

    public function downloadArticleQualify(ArticleFulfillment $articleFulfillment)
    {
        $path = 'fulfillments/'.$this->company."/";
        return Storage::disk('s3_MLegal')->download($path.$articleFulfillment->file);
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

    public function downloadTemplateImport()
    {
        return Excel::download(new LegalMatrixImport($this->company), 'PlantillaImportacionLeyes.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        LawImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }
}
