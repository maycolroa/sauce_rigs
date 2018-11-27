<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\EmployeeProcess;
use App\Http\Requests\Administrative\Processes\ProcessRequest;
use Session;

class EmployeeProcessController extends Controller
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
        $processes = EmployeeProcess::select(
            'sau_employees_processes.id as id',
            'sau_employees_processes.name as name',
            'sau_employees_areas.name as area',
            'sau_employees_headquarters.name as sede',
            'sau_employees_regionals.name as regional'
        )
        ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees_processes.employee_area_id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees_areas.employee_headquarter_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id');

        return Vuetable::of($processes)
                ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Processes\ProcessRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcessRequest $request)
    {
        $process = new EmployeeProcess($request->all());
        
        if(!$process->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el proceso'
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
            $process = EmployeeProcess::findOrFail($id);
            $process->employee_regional_id = $process->area->headquarter->regional->id;
            $process->employee_headquarter_id = $process->area->headquarter->id;
            $process->employee_area_id = $process->area->id;
            $process->multiselect_regional = $process->area->headquarter->regional->multiselect(); 
            $process->multiselect_sede = $process->area->headquarter->multiselect(); 
            $process->multiselect_area = $process->area->multiselect(); 

            return $this->respondHttp200([
                'data' => $process,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Processes\ProcessRequest $request
     * @param  EmployeeProcess $process
     * @return \Illuminate\Http\Response
     */
    public function update(ProcessRequest $request, EmployeeProcess $process)
    {
        $process->fill($request->all());
        
        if(!$process->update()){
          return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el proceso'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeProcess $process
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeProcess $process)
    {
        if (count($process->employees) > 0)
        {
            return $this->respondWithError('No se puede eliminar el proceso porque hay empleados asociados a él');
        }

        if(!$process->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el proceso'
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
            if ($request->has('area') && $request->get('area') != '')
            {
                $keyword = "%{$request->keyword}%";
                $areas = EmployeeProcess::selectRaw(
                    "sau_employees_processes.id as id,
                    sau_employees_processes.name as name")
                ->join('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees_processes.employee_area_id')
                ->where('employee_area_id', $request->get('area'))
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_employees_processes.name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($areas)
                ]);
            }
        }
    }
}
