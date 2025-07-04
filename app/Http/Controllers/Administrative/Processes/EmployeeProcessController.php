<?php

namespace App\Http\Controllers\Administrative\Processes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Http\Requests\Administrative\Processes\ProcessRequest;
use App\Models\Administrative\Processes\TagsProcess;
use Session;
use DB;

class EmployeeProcessController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:processes_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:processes_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:processes_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:processes_d, {$this->team}", ['only' => 'destroy']);
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
        $processes = EmployeeProcess::selectRaw(
            'sau_employees_processes.id as id,
             sau_employees_processes.name as name,
             GROUP_CONCAT(CONCAT(" ", sau_employees_headquarters.name) ORDER BY sau_employees_headquarters.name ASC) as sede,
             sau_employees_regionals.name as regional'
        )
        ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
        ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name', 'sau_employees_regionals.name')
        ->orderBy('sau_employees_processes.id', 'DESC');

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
        DB::beginTransaction();

        try
        { 
            /**CREA LOS TAGS */
            $types = $this->tagsPrepare($request->types);
            $this->tagsSave($types, TagsProcess::class);

            $process = new EmployeeProcess();
            $process->name = $request->name;
            $process->abbreviation = $request->abbreviation;
            $process->types = $types->implode(',');
            $process->save();

            $headquarter_alls = '';

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

            $process->headquarters()->sync($headquarters);

            $this->saveLogActivitySystem('Procesos', 'Se creo el proceso  '.$process->name);

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el registro'
        ]);
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
            $process = EmployeeProcess::findOrFail($id);
            $headquarters = [];

            foreach ($process->headquarters as $key => $value)
            {
                if ($key == 0)
                {
                    $process->employee_regional_id = $value->regional->id;
                    $process->multiselect_regional = $value->regional->multiselect();
                }
                
                array_push($headquarters, $value->multiselect());
            }

            $process->multiselect_employee_headquarter_id = $headquarters;
            $process->employee_headquarter_id = $headquarters;

            return $this->respondHttp200([
                'data' => $process,
            ]);
        } catch(Exception $e){
            return $this->respondHttp500();
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
        DB::beginTransaction();

        try
        { 
            /**CREA LOS TAGS */
            $types = $this->tagsPrepare($request->types);
            $this->tagsSave($types, TagsProcess::class);
            
            $process->name = $request->name;
            $process->abbreviation = $request->abbreviation;
            $process->types = $types->implode(',');
            $process->update();

            $headquarter_alls = '';

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

            $process->headquarters()->sync($headquarters);

            /*$process->headquarters()->sync($this->getDataFromMultiselect($request->get('employee_headquarter_id')));*/

            $this->saveLogActivitySystem('Procesos', 'Se edito el proceso  '.$process->name);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
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
        if (count($process->employees) > 0 || count($process->areas) > 0 || count($process->dangerMatrices) > 0 || count($process->reports) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Procesos', 'Se elimino el proceso  '.$process->name);

        if(!$process->delete())
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
        $headquarters = EmployeeHeadquarter::selectRaw(
            "sau_employees_headquarters.id as id")
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
        ->where('sau_employees_regionals.company_id', $this->company)
        ->pluck('id')
        ->toArray();

        if($request->has('keyword'))
        {
            if ($request->has('headquarter') && $request->get('headquarter') != '')
            {
                $keyword = "%{$request->keyword}%";
                $processes = EmployeeProcess::selectRaw(
                    "sau_employees_processes.id as id,
                    sau_employees_processes.name as name")
                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_employees_processes.name', 'like', $keyword);
                });

                $headquarter = $request->get('headquarter');
                
                if (is_numeric($headquarter))
                    $processes->where('sau_headquarter_process.employee_headquarter_id', $headquarter);
                else
                {
                    $headquarters_select = $this->getValuesForMultiselect($headquarter);

                    if ($request->has('form') && $request->form == 'inspections')
                    {
                        if (in_array('Todos', $headquarters_select->toArray()))
                            $processes->whereIn('sau_headquarter_process.employee_headquarter_id', $headquarters);
                        else
                            $processes->whereIn('sau_headquarter_process.employee_headquarter_id', $headquarters_select);
                    }
                    else
                        $processes->whereIn('sau_headquarter_process.employee_headquarter_id', $headquarters_select);
                }

                $processes = $processes->orderBy('name')->take(30)->get();

                if ($request->has('form') && $request->form == 'inspections')
                    $processes->push(['id' => 'Todos', 'name' => 'Todos']);
                
                $processes = $processes->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($processes)
                ]);
            }
        }
        else
        {
            $processes = EmployeeProcess::selectRaw(
                    "sau_employees_processes.id as id,
                     sau_employees_processes.name as name")
                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_process_id', 'sau_employees_processes.id')
                ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_headquarter_process.employee_headquarter_id')
                ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
                ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name')
                ->orderBy('name')
                ->pluck('id', 'name');
        
            return $this->multiSelectFormat($processes);
        }
    }
}
