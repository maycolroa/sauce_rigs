<?php

namespace App\Http\Controllers\Administrative\Employees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\Administrative\Employees\EmployeeRequest;
use App\Exports\Administrative\Employees\EmployeeImportTemplate;
use App\Exports\Administrative\Employees\EmployeeInactiveTemplate;
use App\Jobs\Administrative\Employees\EmpployeeImportJob;
use App\Jobs\Administrative\Employees\EmployeeImportInactiveJob;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Datetime;

class EmployeesController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:employees_c, {$this->team}", ['only' => ['store', 'import', 'downloadTemplateImport']]);
        $this->middleware("permission:employees_r, {$this->team}", ['except' =>['multiselect', 'multiselectDeal', 'multiselectIdentifications', 'multiselectNames']]);
        $this->middleware("permission:employees_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:employees_d, {$this->team}", ['only' => 'destroy']);
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
        $employees = Employee::select(
            'sau_employees.*',
            'sau_employees_positions.name as position',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_processes.name as process',
            'sau_employees_areas.name as area',
            'sau_employees_businesses.name as business',
            'sau_employees_eps.name as eps',
            'sau_employees_afp.name as afp',
            'sau_employees_arl.name as arl'
        )
        ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_employees.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees.employee_area_id')
        ->leftJoin('sau_employees_businesses', 'sau_employees_businesses.id', 'sau_employees.employee_business_id')
        ->leftJoin('sau_employees_eps', 'sau_employees_eps.id', 'sau_employees.employee_eps_id')
        ->leftJoin('sau_employees_afp', 'sau_employees_afp.id', 'sau_employees.employee_afp_id')
        ->leftJoin('sau_employees_arl', 'sau_employees_arl.id', 'sau_employees.employee_arl_id')
        ->orderBy('sau_employees.id', 'DESC');

        return Vuetable::of($employees)
                ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Employees\EmployeeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $employee = new Employee($request->all());
        $employee->company_id = $this->company;
        $employee->income_date = (Carbon::createFromFormat('D M d Y',$employee->income_date))->format('Ymd');

        if ($employee->date_of_birth)
            $employee->date_of_birth = (Carbon::createFromFormat('D M d Y',$employee->date_of_birth))->format('Ymd');

        if ($employee->last_contract_date)
            $employee->last_contract_date = (Carbon::createFromFormat('D M d Y',$employee->last_contract_date))->format('Ymd');

        if(!$employee->save()){
            return $this->respondHttp500();
        }

        if($this->updateModelLocationForm($employee, $request->get('locations')))
            return $this->respondHttp500();

        $this->saveLogActivitySystem('Empleados', 'Se creo el empleado  '.$employee->name. ' - '.$employee->identification);

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
            $employee = Employee::findOrFail($id);
            $employee->antiquity = $this->timeDifference($employee->income_date);

            $employee->income_date = $employee->income_date ? (Carbon::createFromFormat('Y-m-d',$employee->income_date))->format('D M d Y') : NULL;

            if($this->company != 616) 
                $employee->age = $employee->age ? $employee->age : ($employee->date_of_birth ? $this->timeDifference((Carbon::createFromFormat('Y-m-d',$employee->date_of_birth))->toDateString()) : '');

            if ($employee->date_of_birth)
                $employee->date_of_birth = (Carbon::createFromFormat('Y-m-d',$employee->date_of_birth))->format('D M d Y');

            if ($employee->last_contract_date)
                $employee->last_contract_date = (Carbon::createFromFormat('Y-m-d',$employee->last_contract_date))->format('D M d Y');

            $employee->locations = $this->prepareDataLocationForm($employee);

            $employee->multiselect_cargo = $employee->position->multiselect(); 
            $employee->multiselect_centro_costo = $employee->business ? $employee->business->multiselect() : []; 
            $employee->multiselect_eps = $employee->eps ? $employee->eps->multiselect() : [];
            $employee->multiselect_afp = $employee->afp ? $employee->afp->multiselect() : []; 
            $employee->multiselect_arl = $employee->arl ? $employee->arl->multiselect() : []; 

            return $this->respondHttp200([
                'data' => $employee,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Employees\EmployeeRequest $request
     * @param  Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        if($this->company != 616) 
            $employee->fill($request->except('age'));
        else
            $employee->fill($request->all());
            
        $employee->income_date = (Carbon::createFromFormat('D M d Y',$employee->income_date))->format('Ymd');

        if ($employee->date_of_birth)
            $employee->date_of_birth = (Carbon::createFromFormat('D M d Y',$employee->date_of_birth))->format('Ymd');

        if ($employee->last_contract_date)
            $employee->last_contract_date = (Carbon::createFromFormat('D M d Y',$employee->last_contract_date))->format('Ymd');

        if(!$employee->update()){
            return $this->respondHttp500();
        }

        if($this->updateModelLocationForm($employee, $request->get('locations')))
            return $this->respondHttp500();

            $this->saveLogActivitySystem('Empleados', 'Se edito el empleado  '.$employee->name. ' - '.$employee->identification);

        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        if (count($employee->audiometries) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        if (count($employee->reports) > 0)
        {
            return $this->respondWithError('No se puede eliminar el registro porque hay otros registros asociados a el');
        }

        $this->saveLogActivitySystem('Empleados', 'Se elimino el empleado  '.$employee->name. ' - '.$employee->identification);

        if (count($employee->reports) > 0)
        {
            foreach ($employee->reports as $key => $report) 
            {
                $this->saveLogActivitySystem('Empleados', 'Al eliminar el empleado  '.$employee->name. ' - '.$employee->identification.', se borro el reporte con el diagnostico ',$check->report->description);
            }
        }

        if(!$employee->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el registro'
        ]);
    }

    public function multiselect(Request $request){
        $keyword = "%{$request->keyword}%";
        $employees = Employee::selectRaw("
            sau_employees.id as id,
            CONCAT(sau_employees.identification, ' - ', sau_employees.name) as name
        ")
        ->where(function ($query) use ($keyword) {
            $query->orWhere('identification', 'like', $keyword)
            ->orWhere('name', 'like', $keyword);
        })  
        ->active()
        ->orderBy('name')
        ->take(30)->pluck('id', 'name');
        return $this->respondHttp200([
            'options' => $this->multiSelectFormat($employees)
        ]);
    }

    public function multiselectEmployee(Request $request)
    {
        $elements = Employee::selectRaw("
                sau_employees.id as id,
                CONCAT(sau_employees.identification, ' - ', sau_employees.name) as name
            ")            
            ->active()
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($elements);
    }

    public function multiselectDeal(Request $request)
    {
        $deals = Employee::selectRaw(
                    "DISTINCT sau_employees.deal AS deal"
                )
                ->whereNotNull('sau_employees.deal')
                ->orderBy('deal')
                ->pluck('deal', 'deal');
            
        return $this->multiSelectFormat($deals);
    }

    public function multiselectIdentifications(Request $request)
    {
        $identifications = Employee::selectRaw(
                    "DISTINCT sau_employees.identification AS identification"
                )
                ->active()
                ->whereNotNull('sau_employees.identification')
                ->orderBy('identification')
                ->pluck('identification', 'identification');
            
        return $this->multiSelectFormat($identifications);
    }

    public function multiselectNames(Request $request)
    {
        $names = Employee::selectRaw(
                    "DISTINCT sau_employees.name AS name"
                )
                ->active()
                ->whereNotNull('sau_employees.name')
                ->orderBy('name')
                ->pluck('name', 'name');
            
        return $this->multiSelectFormat($names);
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new EmployeeImportTemplate($this->company), 'PlantillaImportacionEmpleados.xlsx');
    }

    public function downloadTemplateInactiveImport()
    {
      return Excel::download(new EmployeeInactiveTemplate(collect([]), $this->company), 'PlantillaInactivacionEmpleados.xlsx');
    }

    /**
     * import.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try
      {
        EmpployeeImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function importInactive(Request $request)
    {
      try
      {
        EmployeeImportInactiveJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    private function timeDifference($startDate, $endDate = null)
    {
        $start = new DateTime($startDate);
        $end;

        if ($endDate == null)
            $end = new DateTime();
        else
            $start = new DateTime($endDate);

        $interval = $start->diff($end);

        return $interval->format('%y años %m meses y %d dias');
    }

    public function toggleState(Employee $employee)
    {
        try
        {
            $newState = $employee->isActive() ? "NO" : "SI";
            $data = ['active' => $newState];

            if (!$employee->update($data)) {
                return $this->respondHttp500();
            }
            
            return $this->respondHttp200([
                'message' => 'Se cambio el estado del empleado'
            ]);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }
}
