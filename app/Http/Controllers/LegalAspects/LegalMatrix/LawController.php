<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Http\Requests\LegalAspects\LegalMatrix\LawRequest;
use Carbon\Carbon;
use Session;
use Validator;
use DB;

class LawController extends Controller
{
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
            $laws = Law::select(
                'sau_lm_laws.*',
                'sau_lm_laws_types.name AS law_type',
                'sau_lm_risks_aspects.name AS risk_aspect',
                'sau_lm_entities.name AS entity',
                'sau_lm_sst_risks.name AS sst_risk'
            )
            ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
            ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
            ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
            ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
            ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
            ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
            ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
            ->groupBy('sau_lm_laws.id');
        }
        else 
        {
            $laws = Law::select(
                    'sau_lm_laws.*',
                    'sau_lm_laws_types.name AS law_type',
                    'sau_lm_risks_aspects.name AS risk_aspect',
                    'sau_lm_entities.name AS entity',
                    'sau_lm_sst_risks.name AS sst_risk'
                )
                ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
                ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
                ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
                ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id');
        }

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $laws->inLawTypes($this->getValuesForMultiselect($filters["lawTypes"]), $filters['filtersType']['lawTypes']);
            $laws->inRiskAspects($this->getValuesForMultiselect($filters["riskAspects"]), $filters['filtersType']['riskAspects']);
            $laws->inEntities($this->getValuesForMultiselect($filters["entities"]), $filters['filtersType']['entities']);
            $laws->inSstRisks($this->getValuesForMultiselect($filters["sstRisks"]), $filters['filtersType']['sstRisks']);
            $laws->inApplySystem($this->getValuesForMultiselect($filters["applySystem"]), $filters['filtersType']['applySystem']);
            $laws->inLawNumbers($this->getValuesForMultiselect($filters["lawNumbers"]), $filters['filtersType']['lawNumbers']);
            $laws->inLawYears($this->getValuesForMultiselect($filters["lawYears"]), $filters['filtersType']['lawYears']);
            $laws->inRepealed($this->getValuesForMultiselect($filters["repealed"]), $filters['filtersType']['repealed']);
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
            
            if(!$law->save()){
                return $this->respondHttp500();
            }

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
            $law->old_file = $law->file;
            $law->multiselect_law_type = $law->lawType->multiselect();
            $law->multiselect_risk_aspect = $law->riskAspect->multiselect();
            $law->multiselect_entity = $law->entity->multiselect();
            $law->multiselect_sst_risk = $law->sstRisk->multiselect();
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
                Article::destroy($request->delete);

            DB::commit();

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

    public function lmLawYears(Request $request)
    {
        $lawYears = Law::selectRaw(
            'DISTINCT(sau_lm_laws.law_year) as law_year'
        )
        ->pluck('law_year', 'law_year');
    
        return $this->multiSelectFormat($lawYears);
    }

    public function lmLawNumbers(Request $request)
    {
        $lawNumbers = Law::selectRaw(
            'DISTINCT(sau_lm_laws.law_number) as law_number'
        )
        ->pluck('law_number', 'law_number');
    
        return $this->multiSelectFormat($lawNumbers);
    }
}
