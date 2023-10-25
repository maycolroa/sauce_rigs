<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\DiseaseOrigin;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\DiseaseOriginRequest;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\SyncReincOptionsSelectJob;

class DiseaseOriginController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_disease_origin_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:reinc_disease_origin_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:reinc_disease_origin_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:reinc_disease_origin_d, {$this->team}", ['only' => 'destroy']);
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
        $diseaseOrigins = DiseaseOrigin::select('*')->orderBy('id', 'DESC');

        return Vuetable::of($diseaseOrigins)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\DiseaseOriginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiseaseOriginRequest $request)
    {
        $diseaseOrigin = new DiseaseOrigin($request->all());
        $diseaseOrigin->company_id = $this->company;
        
        if(!$diseaseOrigin->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Tipo de evento', 'Se creo el tipo de evento '. $diseaseOrigin->name);

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_disease_origin', $diseaseOrigin->getTable());

        return $this->respondHttp200([
            'message' => 'Se creo el Tipo de evento'
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
            $diseaseOrigin = DiseaseOrigin::findOrFail($id);

            return $this->respondHttp200([
                'data' => $diseaseOrigin,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\DiseaseOriginRequest  $request
     * @param  DiseaseOrigin  $diseaseOrigin
     * @return \Illuminate\Http\Response
     */
    public function update(DiseaseOriginRequest $request, DiseaseOrigin $diseaseOrigin)
    {
        $diseaseOrigin->fill($request->all());
        
        if(!$diseaseOrigin->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Tipo de evento', 'Se edito el tipo de evento '. $diseaseOrigin->name);
        
        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_disease_origin', $diseaseOrigin->getTable());

        return $this->respondHttp200([
            'message' => 'Se actualizo el Tipo de evento'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  DiseaseOrigin  $diseaseOrigin
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiseaseOrigin $diseaseOrigin)
    {
        $this->saveLogActivitySystem('Reincorporaciones - Tipo de evento', 'Se elimino el tipo de evento '. $diseaseOrigin->name);

        if (!$diseaseOrigin->delete())
            return $this->respondHttp500();

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_disease_origin', $diseaseOrigin->getTable());
        
        return $this->respondHttp200([
            'message' => 'Se elimino el Tipo de evento'
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
            $diseaseOrigins = DiseaseOrigin::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($diseaseOrigins)
            ]);
        }
        else
        {
            $diseaseOrigins = DiseaseOrigin::select(
                'sau_reinc_disease_origin.id as id',
                'sau_reinc_disease_origin.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($diseaseOrigins);
        }
    }
}
