<?php

namespace App\Http\Controllers\Administrative\Processes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Processes\EmployeeProcess;
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
        ->groupBy('sau_employees_processes.id', 'sau_employees_processes.name', 'sau_employees_regionals.name');

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
            $process->types = $types->implode(',');
            $process->save();

            $process->headquarters()->sync($this->getDataFromMultiselect($request->get('employee_headquarter_id')));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
            //return $this->respondHttp500();
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
            $process->types = $types->implode(',');
            $process->update();

            $process->headquarters()->sync($this->getDataFromMultiselect($request->get('employee_headquarter_id')));

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
        if (count($process->employees) > 0 || count($process->areas) > 0 || count($process->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

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
                    $query->orWhere('sau_employees_headquarters.name', 'like', $keyword);
                });

                $headquarter = $request->get('headquarter');
                
                if (is_numeric($headquarter))
                    $processes->where('sau_headquarter_process.employee_headquarter_id', $headquarter);
                else
                    $processes->whereIn('sau_headquarter_process.employee_headquarter_id', $this->getValuesForMultiselect($headquarter));

                $processes = $processes->orderBy('name')->take(30)->pluck('id', 'name');

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
