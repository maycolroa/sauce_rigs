<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeeRegional;
use App\Http\Requests\Administrative\Regionals\RegionalRequest;
use Session;

class EmployeeRegionalController extends Controller
{
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
            'message' => 'Se creo la regional'
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
            'message' => 'Se actualizo la regional'
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
        if (count($regional->employees) > 0 /*|| count($regional->sedes) > 0*/)
        {
            return $this->respondWithError('No se puede eliminar la regional porque hay empleados/sedes asociados a ella');
        }

        if(!$regional->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la regional'
        ]);
    }

    public function multiselect(){
        $areas = EmployeeRegional::selectRaw("
            sau_employees_regionals.id as id,
            sau_employees_regionals.name as name
        ")->pluck('id', 'name');
        
        return $this->multiSelectFormat($areas);
    }
}
