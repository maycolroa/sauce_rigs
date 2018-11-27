<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeeArea;
use App\Http\Requests\Administrative\Areas\AreaRequest;
use Session;

class EmployeeAreaController extends Controller
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
        $headquarters = EmployeeArea::select(
            'sau_employees_areas.id as id',
            'sau_employees_areas.name as name',
            'sau_employees_headquarters.name as sede',
            'sau_employees_regionals.name as regional'
        )
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees_areas.employee_headquarter_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id');

        return Vuetable::of($headquarters)
                ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\AreaRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaRequest $request)
    {
        $area = new EmployeeArea($request->all());
        
        if(!$area->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el área'
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
            $area = EmployeeArea::findOrFail($id);
            $area->employee_regional_id = $area->headquarter->regional->id;
            $area->multiselect_regional = $area->headquarter->regional->multiselect(); 
            $area->multiselect_sede = $area->headquarter->multiselect(); 

            return $this->respondHttp200([
                'data' => $area,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Headquarters\AreaRequest $request
     * @param  EmployeeArea $area
     * @return \Illuminate\Http\Response
     */
    public function update(AreaRequest $request, EmployeeArea $area)
    {
        $area->fill($request->all());
        
        if(!$area->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el área'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeArea $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeArea $area)
    {
        if (count($area->employees) > 0 || count($area->processes) > 0)
        {
            return $this->respondWithError('No se puede eliminar el área porque hay empleados/procesos asociados a ella');
        }

        if(!$area->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el área'
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
            if ($request->has('headquarter') && $request->get('headquarter') != '')
            {
                $keyword = "%{$request->keyword}%";
                $areas = EmployeeArea::selectRaw(
                    "sau_employees_areas.id as id,
                    sau_employees_areas.name as name")
                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees_areas.employee_headquarter_id')
                ->where('employee_headquarter_id', $request->get('headquarter'))
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_employees_areas.name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($areas)
                ]);
            }
        }
        else
        {
            $areas = EmployeeArea::selectRaw(
                    "sau_employees_areas.id as id,
                    CONCAT(sau_employees_regionals.name, ' / ', sau_employees_headquarters.name, ' / ', sau_employees_areas.name) as name")
                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees_areas.employee_headquarter_id')
                ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')->pluck('id', 'name');
        
            return $this->multiSelectFormat($areas);
        }
    }
}
