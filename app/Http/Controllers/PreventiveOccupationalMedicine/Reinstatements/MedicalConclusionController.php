<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\MedicalConclusion;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\MedicalConclusionRequest;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\SyncReincOptionsSelectJob;

class MedicalConclusionController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_medical_conclusion_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:reinc_medical_conclusion_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:reinc_medical_conclusion_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:reinc_medical_conclusion_d, {$this->team}", ['only' => 'destroy']);
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
        $medicalConclusions = MedicalConclusion::select('*');

        return Vuetable::of($medicalConclusions)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\MedicalConclusionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MedicalConclusionRequest $request)
    {
        $medicalConclusion = new MedicalConclusion($request->all());
        $medicalConclusion->company_id = $this->company;
        
        if(!$medicalConclusion->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Conclusiones medicas', 'Se creo la conclusion medica '. $medicalConclusion->name);

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_medical_conclusions', $medicalConclusion->getTable());

        return $this->respondHttp200([
            'message' => 'Se creo la Conclusion médica'
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
            $medicalConclusion = MedicalConclusion::findOrFail($id);

            return $this->respondHttp200([
                'data' => $medicalConclusion,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\MedicalConclusionRequest  $request
     * @param  MedicalConclusion  $medicalConclusion
     * @return \Illuminate\Http\Response
     */
    public function update(MedicalConclusionRequest $request, MedicalConclusion $medicalConclusion)
    {
        $medicalConclusion->fill($request->all());
        
        if(!$medicalConclusion->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Reincorporaciones - Conclusiones medicas', 'Se edito la conclusion medica '. $medicalConclusion->name);
        
        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_medical_conclusions', $medicalConclusion->getTable());

        return $this->respondHttp200([
            'message' => 'Se actualizo la Conclusion médica'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  MedicalConclusion  $medicalConclusion
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalConclusion $medicalConclusion)
    {
        $this->saveLogActivitySystem('Reincorporaciones - Conclusiones medicas', 'Se elimino la conclusion medica '. $medicalConclusion->name);

        if (!$medicalConclusion->delete())
            return $this->respondHttp500();

        SyncReincOptionsSelectJob::dispatch($this->company, 'reinc_select_medical_conclusions', $medicalConclusion->getTable());
        
        return $this->respondHttp200([
            'message' => 'Se elimino la Conclusion médica'
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
            $medicalConclusions = MedicalConclusion::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($medicalConclusions)
            ]);
        }
        else
        {
            $medicalConclusions = MedicalConclusion::select(
                'sau_reinc_medical_conclusions.id as id',
                'sau_reinc_medical_conclusions.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($medicalConclusions);
        }
    }
}
