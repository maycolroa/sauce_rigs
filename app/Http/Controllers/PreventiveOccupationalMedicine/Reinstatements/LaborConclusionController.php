<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborConclusion;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\LaborConclusionRequest;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\SyncReincOptionsSelectJob;

class LaborConclusionController extends Controller
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
        $laborConclusions = LaborConclusion::select('*');

        return Vuetable::of($laborConclusions)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\LaborConclusionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LaborConclusionRequest $request)
    {
        $laborConclusion = new LaborConclusion($request->all());
        $laborConclusion->company_id = $this->company;
        
        if(!$laborConclusion->save()){
            return $this->respondHttp500();
        }

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_labor_conclusions', $laborConclusion->getTable());

        return $this->respondHttp200([
            'message' => 'Se creo la Conclusion laboral'
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
            $laborConclusion = LaborConclusion::findOrFail($id);

            return $this->respondHttp200([
                'data' => $laborConclusion,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\LaborConclusionRequest  $request
     * @param  LaborConclusion  $laborConclusion
     * @return \Illuminate\Http\Response
     */
    public function update(LaborConclusionRequest $request, LaborConclusion $laborConclusion)
    {
        $laborConclusion->fill($request->all());
        
        if(!$laborConclusion->update()){
          return $this->respondHttp500();
        }
        
        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_labor_conclusions', $laborConclusion->getTable());

        return $this->respondHttp200([
            'message' => 'Se actualizo la Conclusion laboral'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  LaborConclusion  $laborConclusion
     * @return \Illuminate\Http\Response
     */
    public function destroy(LaborConclusion $laborConclusion)
    {
        if (!$laborConclusion->delete())
            return $this->respondHttp500();

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_labor_conclusions', $laborConclusion->getTable());
        
        return $this->respondHttp200([
            'message' => 'Se elimino la Conclusion laboral'
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
            $laborConclusions = LaborConclusion::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($laborConclusions)
            ]);
        }
        else
        {
            $laborConclusions = LaborConclusion::select(
                'sau_reinc_labor_conclusions.id as id',
                'sau_reinc_labor_conclusions.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($laborConclusions);
        }
    }
}
