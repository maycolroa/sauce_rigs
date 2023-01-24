<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\RiskAspect;
use App\Http\Requests\LegalAspects\LegalMatrix\RiskAspectRequest;

class RiskAspectController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:risksAspects_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:risksAspects_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:risksAspects_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:risksAspects_d, {$this->team}", ['only' => 'destroy']);
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
        $risk_aspects = RiskAspect::select('*');

        return Vuetable::of($risk_aspects)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RiskAspectRequest $request)
    {
        $risk_aspect = new RiskAspect($request->all());
        
        if(!$risk_aspect->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz legal - Tema Ambiental', 'Se creo el tema ambiental '.$risk_aspect->name);

        return $this->respondHttp200([
            'message' => 'Se creo el Riesgo/Aspecto Ambiental'
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
            $risk_aspect = RiskAspect::findOrFail($id);

            return $this->respondHttp200([
                'data' => $risk_aspect,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(RiskAspectRequest $request, RiskAspect $riskAspect)
    {
        $riskAspect->fill($request->all());
        
        if(!$riskAspect->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz legal - Tema Ambiental', 'Se edito el tema ambiental '.$riskAspect->name);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el Riesgo/Aspecto Ambiental'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(RiskAspect $riskAspect)
    {
        if (COUNT($riskAspect->laws) > 0)
        {
            return $this->respondWithError('No se puede eliminar el Riesgo/Aspecto Ambiental porque hay registros asociados a él');
        }

        $this->saveLogActivitySystem('Matriz legal - Tema Ambiental', 'Se elimino el tema ambiental '.$riskAspect->name);

        if(!$riskAspect->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el Riesgo/Aspecto Ambiental'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $risk_aspects = RiskAspect::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($risk_aspects)
            ]);
        }
        else
        {
            $risk_aspects = RiskAspect::select(
                'sau_lm_risks_aspects.id as id',
                'sau_lm_risks_aspects.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($risk_aspects);
        }
    }
}
