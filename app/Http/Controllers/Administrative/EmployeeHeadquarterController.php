<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeeHeadquarter;
use App\Http\Requests\Administrative\Headquarters\HeadquarterRequest;
use Session;

class EmployeeHeadquarterController extends Controller
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
        $headquarters = EmployeeHeadquarter::select(
            'sau_employees_headquarters.id as id',
            'sau_employees_headquarters.name as name',
            'sau_employees_regionals.name as regional'
        )->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id');

        return Vuetable::of($headquarters)
                ->make();
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\HeadquarterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HeadquarterRequest $request)
    {
        $headquarter = new EmployeeHeadquarter($request->all());
        
        if(!$headquarter->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la sede'
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
            $headquarter = EmployeeHeadquarter::findOrFail($id);

            $headquarter->multiselect_regional = $headquarter->regional->multiselect(); 

            return $this->respondHttp200([
                'data' => $headquarter,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\HeadquarterRequest  $request
     * @param  EmployeeHeadquarter  $headquarter
     * @return \Illuminate\Http\Response
     */
    public function update(HeadquarterRequest $request, EmployeeHeadquarter $headquarter)
    {
        $headquarter->fill($request->all());
        
        if(!$headquarter->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la sede'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeHeadquarter $headquarter
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeHeadquarter $headquarter)
    {
        /*if (count($headquarter->employees) > 0)
        {
            return $this->respondWithError('No se puede eliminar el centro de costo porque hay empleados asociados a él');
        }*/

        if(!$headquarter->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la sede'
        ]);
    }
}
