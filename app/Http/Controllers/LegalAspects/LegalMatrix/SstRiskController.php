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
        $this->middleware('permission:sstRisks_c', ['only' => 'store']);
        $this->middleware('permission:sstRisks_r', ['except' =>'multiselect']);
        $this->middleware('permission:sstRisks_u', ['only' => 'update']);
        $this->middleware('permission:sstRisks_d', ['only' => 'destroy']);
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
            'message' => 'Se creo el Tema SST'
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
            'message' => 'Se actualizo el Tema SST'
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
        if (COUNT($sstRisk->laws) > 0)
        {
            return $this->respondWithError('No se puede eliminar el Tema SST porque hay registros asociados a él');
        }

        if(!$sstRisk->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el Tema SST'
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
        else
        {
            $sst_risks = SstRisk::select(
                'sau_lm_sst_risks.id as id',
                'sau_lm_sst_risks.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($sst_risks);
        }
    }
}
