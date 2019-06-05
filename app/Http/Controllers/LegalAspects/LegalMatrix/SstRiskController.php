<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\SstRisk;
use App\Http\Requests\LegalAspects\LegalMatrix\SstRiskRequest;
use Session;

class SstRiskController extends Controller
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
        $sst_risks = SstRisk::select('*');

        return Vuetable::of($sst_risks)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SstRiskRequest $request)
    {
        $sst_risk = new SstRisk($request->all());
        
        if(!$sst_risk->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el Riesgo/Aspecto ambiental'
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
            $sst_risk = SstRisk::findOrFail($id);

            return $this->respondHttp200([
                'data' => $sst_risk,
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
    public function update(SstRiskRequest $request, SstRisk $sstRisk)
    {
        $sstRisk->fill($request->all());
        
        if(!$sstRisk->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el Riesgo/Aspecto ambiental'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(SstRisk $sstRisk)
    {
        if(!$sstRisk->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el Riesgo/Aspecto ambiental'
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
            $sst_risks = SstRisk::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($sst_risks)
            ]);
        }
    }
}
