<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\Risk;
use App\Http\Requests\IndustrialSecure\RiskMatrix\RiskRequest;

class RiskController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:risks_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:risks_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:risks_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:risks_d, {$this->team}", ['only' => 'destroy']);
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
        $risks = Risk::select('*')->where('company_id', $this->company);

        return Vuetable::of($risks)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RiskRequest $request)
    {
        $risk = new Risk($request->all());
        $risk->company_id = $this->company;
        
        if(!$risk->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el riesgo'
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
            $risk = Risk::findOrFail($id);

            return $this->respondHttp200([
                'data' => $risk,
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
    public function update(RiskRequest $request, Risk $risk)
    {
        $risk->fill($request->all());
        
        if(!$risk->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el riesgo'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Risk $risk)
    {
        /*if (count($risk->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar la actividad porque hay matrices de peligro asociadas a ella');
        }*/

        if(!$risk->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el riesgo'
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
            $sub_processes = Risk::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where('company_id', $this->company)
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($sub_processes)
            ]);
        }
        else
        {
            $sub_processes = Risk::selectRaw("
                sau_rm_risk.id as id,
                sau_rm_risk.name as name
            ")
            ->where('company_id', $this->company)
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($sub_processes);
        }
    }
}
