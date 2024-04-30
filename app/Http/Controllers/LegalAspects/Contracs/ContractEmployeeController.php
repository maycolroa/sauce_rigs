<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Http\Requests\LegalAspects\Contracts\ContractEmployeeRequest;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ProyectContract;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesImport;
use App\Jobs\LegalAspects\Contracts\Employees\ContractEmployeeImportJob;
use App\Traits\ContractTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use Validator;
use Hash;
use DB;

class ContractEmployeeController extends Controller
{
    use ContractTrait, Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_employee_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_employee_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:contracts_employee_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_employee_d, {$this->team}", ['only' => 'destroy']);
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
        $employees = ContractEmployee::select(
            'sau_ct_contract_employees.id AS id',
            'sau_ct_contract_employees.name AS name',
            'sau_ct_contract_employees.email AS email',
            'sau_ct_contract_employees.position AS position',
            'sau_ct_contract_employees.identification AS identification',
            'sau_ct_contract_employees.state as state',
            DB::raw('GROUP_CONCAT(CONCAT(" ", sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) as proyects')
        )
        ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
        ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
        ->orderBy('sau_ct_contract_employees.id', 'DESC')
        ->groupBy('sau_ct_contract_employees.id');

        $url = "/legalaspects/employees";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["proyects"]))
                $employees->inProyects($this->getValuesForMultiselect($filters["proyects"]), $filters['filtersType']['proyects']);
        }


        if ($request->has('modelId') && $request->get('modelId'))
            $employees->where('sau_ct_contract_employees.contract_id', $request->get('modelId'));
        else 
        {
            $employees->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_contract_employees.contract_id');
            $employees->where('sau_user_information_contract_lessee.user_id', '=', $this->user->id);
        }

        return Vuetable::of($employees)
                    ->make();
    }

    public function dataContract(Request $request)
    {
        $employees = ContractEmployee::select(
            'sau_ct_contract_employees.id AS id',
            'sau_ct_contract_employees.name AS name',
            'sau_ct_contract_employees.email AS email',
            'sau_ct_contract_employees.position AS position',
            'sau_ct_contract_employees.identification AS identification',
            'sau_ct_contract_employees.state as state',
            DB::raw('GROUP_CONCAT(CONCAT(" ", sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) as proyects')
        )
        ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
        ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
        ->groupBy('sau_ct_contract_employees.id')
        ->orderBy('sau_ct_contract_employees.id', 'DESC');

        /*$url = "/legalaspects/employees/view/contract/".$request->get('modelId');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["proyects"]))
                $employees->inProyects($this->getValuesForMultiselect($filters["proyects"]), $filters['filtersType']['proyects']);
        }*/

        if ($request->has('modelId') && $request->get('modelId'))
            $employees->where('sau_ct_contract_employees.contract_id', $request->get('modelId'));
        else 
        {
            $employees->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_contract_employees.contract_id');
            $employees->where('sau_user_information_contract_lessee.user_id', '=', $this->user->id);
        }

        return Vuetable::of($employees)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ContractEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractEmployeeRequest $request)
    {
        Validator::make($request->all(), [
            "activities.*.documents.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                       if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt')
                            $fail('Archivo debe ser un pdf, un excel, un word o una presentación');
                    }
                }

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employee = new ContractEmployee($request->all());

            $contract = $this->getContractUser($this->user->id, $this->company);
            $employee->contract_id = $contract->id;
            $employee->company_id = $this->company;
            $employee->token = Hash::make($employee->email.$employee->identification);
            $tok = str_replace("/", "a", $employee->token);
            $employee->token = $tok;
            $employee->date_of_birth = isset($request->date_of_birth) ? (Carbon::createFromFormat('D M d Y',$request->date_of_birth))->format('Ymd') : NULL;

            if (!$employee->save())
                return $this->respondHttp500();

            $activities = collect([]);
            $documents_complets = false;

            if($request->has('activities'))
            {
                $activities = $this->saveActivities($employee, $request->activities);
                $documents_complets = $this->documentscomplets($employee, $request->activities, $activities['files']);
            }

            if($request->has('proyects_id'))
            {
                $proyects = $this->getDataFromMultiselect($request->proyects_id);
                $employee->proyects()->sync($proyects);
            }

            $employee->activities()->sync($activities['activities']->values());

            $employee->update(
                [ 'state' => $documents_complets ? 'Aprobado' : 'Pendiente']
            );

            TrainingSendNotificationJob::dispatch($this->company, '', $employee->id);

            $this->saveLogActivitySystem('Contratistas - Empleados', 'Se creo el empleado '.$employee->name.' - '.$employee->identification);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el empleado'
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
            $contractEmployee = ContractEmployee::findOrFail($id);
            $contractEmployee->multiselect_afp = $contractEmployee->afp ? $contractEmployee->afp->multiselect() : [];
            $contractEmployee->multiselect_eps = $contractEmployee->eps ? $contractEmployee->eps->multiselect() : [];
            $contractEmployee->date_of_birth = $contractEmployee->date_of_birth ? (Carbon::createFromFormat('Y-m-d',$contractEmployee->date_of_birth))->format('D M d Y') : NULL;

            $activities = $contractEmployee->activities->transform(function($activity, $index) use ($contractEmployee) {
                $activity->key = Carbon::now()->timestamp + rand(1,10000);
                $activity->name = $activity->name;
                $activity->selected = $activity->id;
                $activity->multiselect_activity = $activity->multiselect();
                $activity->documents = $this->getFilesByActivity($activity->id, $contractEmployee->id, $contractEmployee->contract_id);

                return $activity;
            });

            $contractEmployee->activities = $activities;

            $proyects_id = [];

            foreach ($contractEmployee->proyects as $key => $value)
            {                
                array_push($proyects_id, $value->multiselect());
            }

            $contractEmployee->multiselect_proyect = $proyects_id;
            $contractEmployee->proyects_id = $proyects_id;

            $contractEmployee->delete = [
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $contractEmployee,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ContractEmployeeRequest  $request
     * @param  Activity  $contractEmployee
     * @return \Illuminate\Http\Response
     */
    public function update(ContractEmployeeRequest $request, ContractEmployee $employeeContract)
    {
        Validator::make($request->all(), [
            "activities.*.documents.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());

                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt')
                            $fail('Archivo debe ser un pdf, un excel, un word o una presentación');
                    }
                }

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employeeContract->fill($request->all());
            $employeeContract->date_of_birth = isset($request->date_of_birth) ? (Carbon::createFromFormat('D M d Y',$request->date_of_birth))->format('Ymd') : NULL;

            if (!$employeeContract->token)
                $employeeContract->token = Hash::make($employeeContract->email.$employeeContract->identification);

            if(!$employeeContract->update()){
                return $this->respondHttp500();
            }

            $activities = collect([]);
            $documents_complets = false;

            if($request->has('activities'))
            {
                $activities = $this->saveActivities($employeeContract, $request->activities);
                $documents_complets = $this->documentscomplets($employeeContract, $request->activities, $activities['files']);
            }

            $employeeContract->activities()->sync($activities['activities']->values());


            if($request->has('proyects_id'))
            {
                $proyects = $this->getDataFromMultiselect($request->proyects_id);
                $employeeContract->proyects()->sync($proyects);
            }

            $employeeContract->update(
                [ 'state' => $documents_complets ? 'Aprobado' : 'Pendiente']
            );

            if ($request->has('delete'))
            {
                foreach ($request->delete['files'] as $id)
                {
                    $file_delete = FileUpload::find($id);

                    if ($file_delete)
                    {
                        $path = $file_delete->file;
                        $file_delete->delete();
                        Storage::disk('s3')->delete('legalAspects/files/'. $path);
                    }
                }
            }

            $this->saveLogActivitySystem('Contratistas - Empleados', 'Se creo el empleado '.$employeeContract->name.' - '.$employeeContract->identification);

            DB::commit();

            TrainingSendNotificationJob::dispatch($this->company, '', $employeeContract->id);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el empleado'
        ]);
    }

    public function saveActivities($employee, $activitiesList)
    {
        $activities = collect([]);

        foreach ($activitiesList as $activity)
        {
            $activities->push($activity['selected']);
            $files_id = [];

            foreach ($activity['documents'] as $document)
            {
                if (COUNT($document['files']) > 0)
                {
                    $files_names_delete = [];

                    foreach ($document['files'] as $keyF => $file) 
                    {
                        $create_file = true;

                        if (isset($file['id']))
                        {
                            $fileUpload = FileUpload::findOrFail($file['id']);

                            if ($file['old_name'] == $file['file'])
                                $create_file = false;
                            else
                                array_push($files_names_delete, $file['old_name']);
                        }
                        else
                        {
                            $fileUpload = new FileUpload();
                            $fileUpload->user_id = $this->user->id;
                        }

                        if ($create_file)
                        {
                            $file_tmp = $file['file'];
                            $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                            $file_tmp->storeAs('legalAspects/files/', $nameFile, 's3');
                            $fileUpload->file = $nameFile;
                        }

                        $fileUpload->name = $file['name'];
                        $fileUpload->expirationDate = $file['required_expiration_date'] == 'SI' ? ($file['expirationDate'] == null ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd')) : null;

                        if (!$fileUpload->save())
                            return $this->respondHttp500();

                        $ini = Carbon::now()->format('Y-m-d 00:00:00');
                        $end = Carbon::now()->format('Y-m-d 23:59:59');

                        $state = FileModuleState::where('file_id', $fileUpload->id)
                        ->whereRaw("sau_ct_file_module_state.created_at BETWEEN '$ini' AND '$end'")->first();

                        if ($state)
                        {
                          $state->state = 'MODIFICADO';
                          $state->update();
                        }
                        else
                        {
                            $state = new FileModuleState;
                            $state->contract_id = $employee->contract_id;
                            $state->file_id = $fileUpload->id;
                            $state->module = 'Empleados';
                            $state->state = 'CREADO';                            
                            $state->date = date('Y-m-d');
                            $state->save();
                        }

                        $fileUpload->contracts()->sync([$employee->contract_id]);
                        $ids = [];
                        $ids[$document['id']] = ['employee_id' => $employee->id];
                        $files_id[$file['key']] = $fileUpload->id;
                        $fileUpload->documents()->sync($ids);
                    }

                    //Borrar archivos reemplazados
                    foreach ($files_names_delete as $keyf => $file)
                    {
                        Storage::disk('s3')->delete('legalAspects/files/'. $file);
                    }
                }
            }
        }

        $data = [
            'activities' => $activities,
            'files' => $files_id
        ];

        return $data;
    }

    public function documentscomplets($employee, $activitiesList, $documents)
    {
        foreach ($activitiesList as $activity)
        {
            $documents_counts = COUNT($activity['documents']);
            $count = 0;

            foreach ($activity['documents'] as $document)
            {
                if (COUNT($document['files']) > 0 && COUNT($documents) > 0)
                {
                    $count_aprobe = 0;

                    foreach ($document['files'] as $key => $file) 
                    {
                        
                        $fileUpload = FileUpload::findOrFail($documents[$file['key']]);

                        if ($fileUpload->expirationDate && $fileUpload->expirationDate > date('Y-m-d'))
                        {
                            if ($fileUpload->state == 'ACEPTADO')
                                $count_aprobe++;
                        }
                        else if (!$fileUpload->expirationDate)
                        {
                            if ($fileUpload->state == 'ACEPTADO')
                                $count_aprobe++;
                        }
                    }

                    if ($count_aprobe == COUNT($document['files']))
                        $count++;
                }
            }

            if ($documents_counts > $count)
                return false;
        }


        return true;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractEmployee $employeeContract)
    {
        DB::beginTransaction();

        try
        {
            $files = DB::table('sau_ct_file_document_employee')->where('employee_id', $employeeContract->id)->get();

            foreach ($files as $key => $value)
            {
                $file_delete = FileUpload::find($value->file_id);

                if ($file_delete)
                {
                    $path = $file_delete->file;
                    $file_delete->delete();
                    Storage::disk('s3')->delete('legalAspects/files/'. $path);
                }
            }

            $this->saveLogActivitySystem('Contratistas - Empleados', 'Se elimino el empleado '.$employeeContract->name.' - '.$employeeContract->identification);

            if (!$employeeContract->delete())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se elimino el empleado'
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

        $contract = $this->getContractUser($this->user->id, $this->company);

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $activities = ActivityContract::select("id", "name")
                ->join('sau_ct_contracts_activities', 'sau_ct_activities.id', 'sau_ct_contracts_activities.activity_id')
                ->where('sau_ct_contracts_activities.contract_id', $contract->id)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_ct_activities.name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = ActivityContract::selectRaw("id, name")
            ->join('sau_ct_contracts_activities', 'sau_ct_activities.id','sau_ct_contracts_activities.activity_id' )
            ->where('sau_ct_contracts_activities.contract_id', $contract->id)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }

    public function multiselectProyect(Request $request)
    {

        $contract = $this->getContractUser($this->user->id, $this->company);

        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $activities = ProyectContract::select("id", "name")
                ->join('sau_ct_contracts_proyects', 'sau_ct_proyects.id', 'sau_ct_contracts_proyects.proyect_id')
                ->where('sau_ct_contracts_proyects.contract_id', $contract->id)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_ct_proyects.name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = ProyectContract::selectRaw("id, name")
            ->join('sau_ct_contracts_proyects', 'sau_ct_proyects.id','sau_ct_contracts_proyects.proyect_id' )
            ->where('sau_ct_contracts_proyects.contract_id', $contract->id)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }

    public function getFilesForm(Request $request)
    {
        return $this->respondHttp200([
            'data' => $this->getFilesByActivity($request->activity, $request->employee, $request->contract_id)
        ]);
    }

    public function getFilesByActivity($activity, $employee_id, $contract_id)
    {
        $documents = ActivityDocument::where('activity_id', $activity)->where('type', 'Empleado')->get();

        if ($documents->count() > 0)
        {
            $contract = $contract_id;
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id) {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
                $document->files = [];

                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.name AS name',
                    'sau_ct_file_upload_contracts_leesse.file AS file',
                    'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate',
                    'sau_ct_file_upload_contracts_leesse.state AS state',
                    'sau_ct_file_upload_contracts_leesse.reason_rejection AS reason_rejection'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                ->get();

                if ($files)
                {
                    $files->transform(function($file, $index) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->old_name = $file->file;
                        $file->expirationDate = $file->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$file->expirationDate))->format('D M d Y');
                        $file->required_expiration_date = $file->expirationDate == null ? 'NO' : 'SI';
                        $file->state = $file->state;
                        $file->reason_rejection = $file->reason_rejection;

                        return $file;
                    });

                    $document->files = $files;
                }

                return $document;
            });
        }

        return $documents;
    }

    public function downloadTemplateImport()
    {
        $contract = $this->getContractUser($this->user->id, $this->company);

        return Excel::download(new ContractsEmployeesImport($this->company, $contract), 'PlantillaImportacionContratistasEmpleados.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        $contract = $this->getContractUser($this->user->id, $this->company);

        ContractEmployeeImportJob::dispatch($request->file, $this->company, $this->user, $contract);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }
}
