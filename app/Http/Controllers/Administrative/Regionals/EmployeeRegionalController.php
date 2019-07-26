<?php

namespace App\Http\Controllers\Administrative\Regionals;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Http\Requests\Administrative\Regionals\RegionalRequest;
use Session;

class EmployeeRegionalController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:regionals_c', ['only' => 'store']);
        $this->middleware('permission:regionals_r', ['except' =>'multiselect']);
        $this->middleware('permission:regionals_u', ['only' => 'update']);
        $this->middleware('permission:regionals_d', ['only' => 'destroy']);
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
        $regionals = EmployeeRegional::select('*');

        return Vuetable::of($regionals)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionalRequest $request)
    {
        $regional = new EmployeeRegional($request->all());
        $regional->company_id = Session::get('company_id');
        
        if(!$regional->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el registro'
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
            $regional = EmployeeRegional::findOrFail($id);

            return $this->respondHttp200([
                'data' => $regional,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function update(RegionalRequest $request, EmployeeRegional $regional)
    {
        $regional->fill($request->all());
        
        if(!$regional->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeRegional $regional)
    {
        if (count($regional->employees) > 0 || count($regional->headquarters) > 0 || count($regional->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        if(!$regional->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el registro'
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
            $regionals = EmployeeRegional::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($regionals)
            ]);
        }
        else
        {
            $regionals = EmployeeRegional::selectRaw("
                sau_employees_regionals.id as id,
                sau_employees_regionals.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($regionals);
        }
    }
}
