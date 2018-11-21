<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeePosition;
use App\Http\Requests\Administrative\Positions\PositionRequest;
use Session;

class EmployeePositionController extends Controller
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
       $positions = EmployeePosition::select('*');

       return Vuetable::of($positions)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Positions\PositionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionRequest $request)
    {
        $position = new EmployeePosition($request->all());
        $position->company_id = Session::get('company_id');
        
        if(!$position->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el cargo'
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
            $position = EmployeePosition::findOrFail($id);

            return $this->respondHttp200([
                'data' => $position,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Positions\PositionRequest  $request
     * @param  EmployeePosition  $position
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, EmployeePosition $position)
    {
        $position->fill($request->all());
        
        if(!$position->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el cargo'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeePosition  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeePosition $position)
    {
        if (count($position->employees) > 0)
        {
            return $this->respondWithError('No se puede eliminar el cargo porque hay empleados asociados a él');
        }

        if(!$position->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el cargo'
        ]);
    }

    public function multiselect(){
        $positions = EmployeePosition::selectRaw("
            sau_employees_positions.id as id,
            sau_employees_positions.name as name
        ")->pluck('id', 'name');
        
        return $this->multiSelectFormat($positions);
    }
}
