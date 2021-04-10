<?php

namespace App\Http\Controllers\Administrative\Labels;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\General\Keyword;
use App\Models\Administrative\Labels\KeywordCompany;
use App\Http\Requests\Administrative\Labels\LabelRequest;
use DB;

class LabelController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:customLabels_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:customLabels_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:customLabels_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:customLabels_d, {$this->team}", ['only' => 'destroy']);
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
        $labels = KeywordCompany::select(
                'sau_keywords.id AS id',
                'sau_keywords.display_name AS name',
                'sau_keyword_company.display_name AS display_name'
            )
            ->join('sau_keywords', 'sau_keywords.id', 'sau_keyword_company.keyword_id');

        return Vuetable::of($labels)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Labels\LabelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LabelRequest $request)
    {
        $label = new KeywordCompany($request->all());
        $label->company_id = $this->company;
        $label->display_name = $this->getValueLocation($label);
        
        KeywordCompany::where('keyword_id', $label->keyword_id)->delete();

        if(!$label->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se configuro la etiqueta'
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
            $label = KeywordCompany::where('keyword_id', $id)->first();
            $label->display_name = $this->getValueLocation($label, false);
            $label->multiselect_keyword = $label->keyword->multiselect();

            return $this->respondHttp200([
                'data' => $label,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\System\Labels\LabelRequest  $request
     * @param  KeywordCompany  $label
     * @return \Illuminate\Http\Response
     */
    public function update(LabelRequest $request, $id)
    {
        try
        {
            DB::beginTransaction();
            
            KeywordCompany::where('keyword_id', $id)->delete();

            $label = new KeywordCompany($request->all());
            $label->company_id = $this->company;
            $label->display_name = $this->getValueLocation($label);
            
            if(!$label->save()){
                return $this->respondHttp500();
            }

            DB::commit();
        
        } catch(Exception $e){
            DB::rollback();
            $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la etiqueta'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        KeywordCompany::where('keyword_id', $id)->delete();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la etiqueta'
        ]);
    }

    private function getValueLocation(KeywordCompany $keyword, $concat = true)
    {
        $label = $keyword->display_name;

        if (in_array($keyword->keyword_id, [1,2,3,4,6,7,8,9]))
        {
            $label = trim(preg_replace('/^((1.)|(2.)|(3.)|(4.))/', "", $label));

            if ($concat)
            {
                if (in_array($keyword->keyword_id, [1,2]))
                    $label = "1. {$label}";

                if (in_array($keyword->keyword_id, [3,4]))
                    $label = "2. {$label}";

                if (in_array($keyword->keyword_id, [6,7]))
                    $label = "3. {$label}";

                if (in_array($keyword->keyword_id, [8,9]))
                    $label = "4. {$label}";
            }
        }

        return $label;
    }
}
