<?php

namespace App\Http\Controllers\Administrative\Areas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
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
             GROUP_CONCAT(DISTINCT CONCAT(" ", sau_employees_processes.name) ORDER BY sau_employees_processes.name ASC) as proceso,
             GROUP_CONCAT(DISTINCT CONCAT(" ", sau_employees_headquarters.name) ORDER BY sau_employees_headquarters.name ASC) as sede,
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
        ->groupBy('sau_employees_areas.id', 'sau_employees_areas.name', 'sau_employees_regionals.name'/*, 'sau_employees_headquarters.name', 'sau_employees_processes.name'*/)
        ->orderBy('sau_employees_areas.id', 'DESC');

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

            /*$headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));
            $process = $this->getDataFromMultiselect($request->get('employee_process_id'));*/
            //$ids = [];

            if ($request->has('employee_headquarter_id'))
            {
                if (count($request->employee_headquarter_id) > 1)
                {
                    foreach ($request->employee_headquarter_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todos')
                        {
                            $headquarter_alls = 'Todos';
                            break;
                        }
                    }
                }
                else if (count($request->employee_headquarter_id) == 1)
                    $headquarter_alls =  json_decode($request->employee_headquarter_id[0])->value;
            }

            if ($request->has('employee_headquarter_id') && $headquarter_alls == 'Todos')
                $headquarters = $this->getHeadquarter($request->employee_regional_id);

            else if ($request->has('employee_headquarter_id'))
                $headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));

            if ($request->has('employee_process_id'))
            {
                if (count($request->employee_process_id) > 1)
                {
                    foreach ($request->employee_process_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todos')
                        {
                            $process_alls = 'Todos';
                            break;
                        }
                    }
                }
                else if (count($request->employee_process_id) == 1)
                    $process_alls =  json_decode($request->employee_process_id[0])->value;
            }

            if ($request->has('employee_process_id') && $process_alls == 'Todos')
                $processes = $this->getProcess($headquarters);

            else if ($request->has('employee_process_id'))
                $processes = $this->getDataFromMultiselect($request->get('employee_process_id'));

            foreach ($headquarters as $key => $valueH) {

                /*$processes_valid = $this->getProcess($valueH);
                $processes = array_intersect($process, $processes_valid);*/

                foreach ($processes as $valueP)
                {
                    $insert = [$valueP, $area->id, $valueH];
                    DB::insert('insert into sau_process_area (employee_process_id, employee_area_id, employee_headquarter_id) values (?, ?, ?)', $insert);
                }
            }

            /*foreach ($process as $value)
            {
                $ids[$value] = ['employee_headquarter_id'=>$request->get('employee_headquarter_id')];
            }*/

            //$area->processes()->sync($ids);

            $this->saveLogActivitySystem('Areas', 'Se creo el area '.$area->name);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el registro'
        ]);
    }

    private function getProcess($headquarters)
    {
        $processes = EmployeeProcess::selectRaw(
            "sau_employees_processes.id as id")
        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->where('sau_headquarter_process.employee_headquarter_id', $headquarters)
        ->pluck('id')
        ->toArray();

        return $processes;
    }

    private function getHeadquarter($regionals)
    {
        $headquarters = EmployeeHeadquarter::selectRaw(
            "sau_employees_headquarters.id as id")
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->where('employee_regional_id', $regionals)
        ->pluck('id')
        ->toArray();

        return $headquarters;
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
            $headquarters = [];
            $repeat = [];
            $repeat_p = [];

            foreach ($area->processes as $key => $value)
            {

                foreach ($value->headquarters as $key2 => $value2)
                {
                    if ($key == 0)
                    {
                        $area->employee_regional_id = $value2->regional->id;
                        $area->multiselect_regional = $value2->regional->multiselect();
                    }

                    if ($value->pivot->employee_headquarter_id == $value2->id)
                    {
                        if (!in_array($value2->id, $repeat))
                        {
                            array_push($headquarters, $value2->multiselect());
                            array_push($repeat, $value2->id);
                        }
                        /*$area->employee_headquarter_id = $value2->id;
                        $area->multiselect_sede = $value2->multiselect();*/
                        
                        //break;
                    }
                }
                //}

                if (!in_array($value->id, $repeat_p))
                {
                    array_push($process, $value->multiselect());
                    array_push($repeat_p, $value->id);
                }
                
                //array_push($process, $value->multiselect());
            }

            $area->multiselect_employee_process_id = $process;
            $area->employee_process_id = $process;

            $area->multiselect_sede = $headquarters;
            $area->employee_headquarter_id = $headquarters;

            return $this->respondHttp200([
                'data' => $area,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
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

            /*$headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));
            $process = $this->getDataFromMultiselect($request->get('employee_process_id'));*/
            //$ids = [];

            DB::delete("delete from sau_process_area where employee_area_id = $area->id");

            /*foreach ($headquarters as $key => $valueH) {
                $processes_valid = $this->getProcess($valueH);
                $processes = array_intersect($process, $processes_valid);

                foreach ($processes as $valueP)
                {
                    $insert = [$valueP, $area->id, $valueH];
                    DB::insert('insert into sau_process_area (employee_process_id, employee_area_id, employee_headquarter_id) values (?, ?, ?)', $insert);
                }
            }

            /*foreach ($process as $value)
            {
                $ids[$value] = ['employee_headquarter_id'=>$request->get('employee_headquarter_id')];
            }
            
            $area->processes()->sync($ids);*/

            if ($request->has('employee_headquarter_id'))
            {
                if (count($request->employee_headquarter_id) > 1)
                {
                    foreach ($request->employee_headquarter_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todos')
                        {
                            $headquarter_alls = 'Todos';
                            break;
                        }
                    }
                }
                else if (count($request->employee_headquarter_id) == 1)
                    $headquarter_alls =  json_decode($request->employee_headquarter_id[0])->value;
            }

            if ($request->has('employee_headquarter_id') && $headquarter_alls == 'Todos')
                $headquarters = $this->getHeadquarter($request->employee_regional_id);

            else if ($request->has('employee_headquarter_id'))
                $headquarters = $this->getDataFromMultiselect($request->get('employee_headquarter_id'));

            if ($request->has('employee_process_id'))
            {
                if (count($request->employee_process_id) > 1)
                {
                    foreach ($request->employee_process_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todos')
                        {
                            $process_alls = 'Todos';
                            break;
                        }
                    }
                }
                else if (count($request->employee_process_id) == 1)
                    $process_alls =  json_decode($request->employee_process_id[0])->value;
            }

            if ($request->has('employee_process_id') && $process_alls == 'Todos')
                $processes = $this->getProcess($headquarters);

            else if ($request->has('employee_process_id'))
                $processes = $this->getDataFromMultiselect($request->get('employee_process_id'));

            foreach ($headquarters as $key => $valueH) {

                /*$processes_valid = $this->getProcess($valueH);
                $processes = array_intersect($process, $processes_valid);*/

                foreach ($processes as $valueP)
                {
                    $insert = [$valueP, $area->id, $valueH];
                    DB::insert('insert into sau_process_area (employee_process_id, employee_area_id, employee_headquarter_id) values (?, ?, ?)', $insert);
                }
            }

            $this->saveLogActivitySystem('Areas', 'Se edito el area '.$area->name);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
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
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Areas', 'Se elimino el area '.$area->name);

        if(!$area->delete())
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
        $processes = EmployeeProcess::selectRaw(
            "sau_employees_processes.id as id")
        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->where('sau_employees_regionals.company_id', $this->company)
        ->pluck('id')
        ->toArray();

        $headquarters = EmployeeHeadquarter::selectRaw(
            "sau_employees_headquarters.id as id")
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->where('sau_employees_regionals.company_id', $this->company)
        ->pluck('id')
        ->toArray();

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
                    $query->orWhere('sau_employees_areas.name', 'like', $keyword);
                });

                $headquarter = $request->get('headquarter');
                
                if (is_numeric($headquarter))
                    $areas->where('employee_headquarter_id', $headquarter);

                else
                {
                    $headquarters_select = $this->getValuesForMultiselect($headquarter);

                    if ($request->has('form') && $request->form == 'inspections')
                    {
                        if (in_array('Todos', $headquarters_select->toArray()))
                            $areas->whereIn('employee_headquarter_id', $headquarters);
                        else
                            $areas->whereIn('employee_headquarter_id', $headquarters_select);
                    }
                    else
                        $areas->whereIn('employee_headquarter_id', $headquarters_select);
                }
                
                $process = $request->get('process');

                if (is_numeric($process))
                    $areas->where('employee_process_id', $process);

                else
                {
                    $processes_select = $this->getValuesForMultiselect($process);

                    if ($request->has('form') && $request->form == 'inspections')
                    {
                        if (in_array('Todos', $processes_select->toArray()))
                            $areas->whereIn('employee_process_id', $processes);
                        else
                            $areas->whereIn('employee_process_id', $processes_select);
                    }
                    else
                        $areas->whereIn('employee_process_id', $processes_select);
                }

                $areas = $areas->orderBy('name')->take(30)->get();

                if ($request->has('form') && $request->form == 'inspections' && $areas->count() > 0)
                    $areas->push(['id' => 'Todos', 'name' => 'Todos']);
                
                $areas = $areas->pluck('id', 'name');

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
                ->orderBy('name')
                ->pluck('id', 'name');
        
            return $this->multiSelectFormat($areas);
        }
    }
}
