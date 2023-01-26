<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\OriginAdvisor;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\OriginAdvisorRequest;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\SyncReincOptionsSelectJob;

class OriginAdvisorController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_origin_advisor_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:reinc_origin_advisor_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:reinc_origin_advisor_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:reinc_origin_advisor_d, {$this->team}", ['only' => 'destroy']);
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
        $originAdvisors = OriginAdvisor::select('*');

        return Vuetable::of($originAdvisors)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\OriginAdvisorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OriginAdvisorRequest $request)
    {
        $originAdvisor = new OriginAdvisor($request->all());
        $originAdvisor->company_id = $this->company;
        
        if(!$originAdvisor->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Procedencia de recomendaciones', 'Se creo la procedencia '. $originAdvisor->name);

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_origin_advisors', $originAdvisor->getTable());

        return $this->respondHttp200([
            'message' => 'Se creo la Procedencia de recomendaciones'
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
            $originAdvisor = OriginAdvisor::findOrFail($id);

            return $this->respondHttp200([
                'data' => $originAdvisor,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\OriginAdvisorRequest  $request
     * @param  OriginAdvisor  $originAdvisor
     * @return \Illuminate\Http\Response
     */
    public function update(originAdvisorRequest $request, OriginAdvisor $originAdvisor)
    {
        $originAdvisor->fill($request->all());
        
        if(!$originAdvisor->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Procedencia de recomendaciones', 'Se edito la procedencia '. $originAdvisor->name);

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_origin_advisors', $originAdvisor->getTable());
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la Procedencia de recomendaciones'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  OriginAdvisor  $originAdvisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(OriginAdvisor $originAdvisor)
    {
        $this->saveLogActivitySystem('Reincorporaciones - Procedencia de recomendaciones', 'Se elimino la procedencia '. $originAdvisor->name);

        if (!$originAdvisor->delete())
            return $this->respondHttp500();
        
        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_origin_advisors', $originAdvisor->getTable());
        
        return $this->respondHttp200([
            'message' => 'Se elimino la Procedencia de recomendaciones'
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
            $originAdvisors = OriginAdvisor::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($originAdvisors)
            ]);
        }
        else
        {
            $originAdvisors = OriginAdvisor::select(
                'sau_reinc_origin_advisor.id as id',
                'sau_reinc_origin_advisor.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($originAdvisors);
        }
    }
}
