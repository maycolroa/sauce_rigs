<?php

namespace App\Http\Controllers\Administrative\Areas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Http\Requests\Administrative\Areas\AreaRequest;
use DB;

class EmployeeAreaController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:areas_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:areas_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:areas_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:areas_d, {$this->team}", ['only' => 'destroy']);
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
        $areas = EmployeeArea::selectRaw(
            'sau_employees_areas.id as id,
             sau_employees_areas.name as name,
             GROUP_CONCAT(CONCAT(" ", sau_employees_processes.name) ORDER BY sau_employees_processes.name ASC) as proceso,
             sau_employees_headquarters.name as sede,
             sau_employees_regionals.name as regional'
        )
        ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
        ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
        ->join('sau_headquarter_process', function($q) {
            $q->on('sau_headquarter_process.employee_process_id', '=', 'sau_employees_processes.id')
              ->on('sau_process_area.employee_headquarter_id', '=', 'sau_headquarter_process.employee_headquarter_id');
        })
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name', 'sau_employees_regionals.name', 'sau_employees_headquarters.name');

        return Vuetable::of($areas)
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
        DB::beginTransaction();

        try
        { 
            $area = new EmployeeArea($request->all());
            $area->save();

            $process = $this->getDataFromMultiselect($request->get('employee_process_id'));
            $ids = [];

            foreach ($process as $value)
            {
                $ids[$value] = ['employee_headquarter_id'=>$request->get('employee_headquarter_id')];
            }

            $area->processes()->sync($ids);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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
            $process = [];

            foreach ($area->processes as $key => $value)
            {
                if ($key == 0)
                {
                    foreach ($value->headquarters as $key2 => $value2)
                    {
                        if ($value->pivot->employee_headquarter_id == $value2->id)
                        {
                            $area->employee_headquarter_id = $value2->id;
                            $area->multiselect_sede = $value2->multiselect();
                            $area->employee_regional_id = $value2->regional->id;
                            $area->multiselect_regional = $value2->regional->multiselect();
                            break;
                        }
                    }
                }
                
                array_push($process, $value->multiselect());
            }

            $area->multiselect_employee_process_id = $process;
            $area->employee_process_id = $process;

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
        DB::beginTransaction();

        try
        { 
            $area->fill($request->all());
            $area->update();

            $process = $this->getDataFromMultiselect($request->get('employee_process_id'));
            $ids = [];

            foreach ($process as $value)
            {
                $ids[$value] = ['employee_headquarter_id'=>$request->get('employee_headquarter_id')];
            }
            
            $area->processes()->sync($ids);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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
        if (count($area->employees) > 0 || count($area->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar el área porque hay registros asociados a ella');
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
            if ($request->has('process') && $request->get('process') != '' && $request->has('headquarter') && $request->get('headquarter') != '')
            {
                $keyword = "%{$request->keyword}%";
                $areas = EmployeeArea::selectRaw(
                    "sau_employees_areas.id as id,
                    sau_employees_areas.name as name")
                ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
                ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_employees_processes.name', 'like', $keyword);
                });

                $headquarter = $request->get('headquarter');
                
                if (is_numeric($headquarter))
                    $areas->where('employee_headquarter_id', $headquarter);
                else
                    $areas->whereIn('employee_headquarter_id', $this->getValuesForMultiselect($headquarter));
                
                $process = $request->get('process');

                if (is_numeric($process))
                    $areas->where('employee_process_id', $process);
                else
                    $areas->whereIn('employee_process_id', $this->getValuesForMultiselect($process));

                $areas = $areas->take(30)->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($areas)
                ]);
            }
        }
        else
        {
            $areas = EmployeeArea::selectRaw(
                    "sau_employees_areas.id as id,
                     sau_employees_areas.name as name")
                ->join('sau_process_area', 'sau_process_area.employee_area_id', 'sau_employees_areas.id')
                ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_process_area.employee_process_id')
                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
                ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name')
                ->pluck('id', 'name');
        
            return $this->multiSelectFormat($areas);
        }
    }
}
