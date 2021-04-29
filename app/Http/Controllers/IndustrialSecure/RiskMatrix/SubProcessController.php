<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\SubProcess;
use App\Http\Requests\IndustrialSecure\RiskMatrix\SubProcessRequest;

class SubProcessController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:subProcesses_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:subProcesses_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:subProcesses_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:subProcesses_d, {$this->team}", ['only' => 'destroy']);
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
        $sub_processes = SubProcess::select('*');

        return Vuetable::of($sub_processes)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubProcessRequest $request)
    {
        $subProcess = new SubProcess($request->all());
        $subProcess->company_id = $this->company;
        
        if(!$subProcess->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la actividad'
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
            $subProcess = SubProcess::findOrFail($id);

            return $this->respondHttp200([
                'data' => $subProcess,
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
    public function update(SubProcessRequest $request, SubProcess $subProcess)
    {
        $subProcess->fill($request->all());
        
        if(!$subProcess->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el subproceso'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubProcess $subProcess)
    {
        /*if (count($subProcess->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar la actividad porque hay matrices de peligro asociadas a ella');
        }*/

        if(!$subProcess->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la actividad'
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
            $sub_processes = SubProcess::select("id", "name")
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
            $sub_processes = SubProcess::selectRaw("
                sau_rm_sub_processes.id as id,
                sau_rm_sub_processes.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($sub_processes);
        }
    }
}
