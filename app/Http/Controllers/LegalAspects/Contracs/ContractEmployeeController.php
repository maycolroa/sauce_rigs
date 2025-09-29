<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Http\Requests\LegalAspects\Contracts\ContractEmployeeRequest;
use App\Http\Requests\LegalAspects\Contracts\ContractEmployeeInactiveRequest;
use App\Http\Requests\LegalAspects\Contracts\ContractEmployeeLiquidatedRequest;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ProyectContract;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
use App\Exports\LegalAspects\Contracts\Contracts\ContractsEmployeesImport;
use App\Exports\Administrative\Employees\EmployeeInactiveTemplate;
use App\Jobs\LegalAspects\Contracts\Employees\ContractEmployeeImportJob;
use App\Jobs\LegalAspects\Contracts\Employees\ContractEmployeeImportSocialSecureJob;
use App\Traits\ContractTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use Validator;
use Hash;
use DB;
use PdfMerger;
use Session;
use Illuminate\Support\Facades\File;

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
            DB::raw('GROUP_CONCAT(CONCAT(" ", sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) as proyects'),
            DB::raw("case when sau_ct_contract_employees.state_employee is true then 'Activo' else 'Inactivo' end as state_employee"),
            DB::raw("case when sau_ct_contract_employees.liquidated is true then 'SI' else 'NO' end as liquidated")
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
            $employees->where('sau_ct_contract_employees.contract_id', $this->getContractIdUser($this->user->id));
            /*$employees->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_contract_employees.contract_id');
            $employees->where('sau_user_information_contract_lessee.user_id', '=', $this->user->id);*/
        }

        return Vuetable::of($employees)
            ->addColumn('legalaspects-contracts-employees-edit', function ($employee) {
                if ($employee->state_employee == 'Inactivo')
                    return false;
                else
                    return true;
            })
            ->addColumn('legalaspects-contracts-employees-liquidated', function ($employee) {
                if ($employee->state_employee == 'Inactivo')
                    return true;
                else
                    return false;
            })
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
            'sau_ct_contract_employees.company_id as company_id',
            DB::raw('GROUP_CONCAT(CONCAT(" ", sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) as proyects'),
            DB::raw("case when sau_ct_contract_employees.state_employee is true then 'Activo' else 'Inactivo' end as state_employee"),
            DB::raw("case when sau_ct_contract_employees.liquidated is true then 'SI' else 'NO' end as liquidated")
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
            ->addColumn('legalaspects-contracts-employees-switchStatus', function ($employee) {
                if ($employee->company_id == 722)
                    return true;
                else
                    return false;
            })
            ->addColumn('legalaspects-contracts-employees-switchStatus-view', function ($employee) {
                if ($employee->state_employee == 'Inactivo')
                    return true;
                else
                    return false;
            })
            ->addColumn('legalaspects-contracts-employees-liquidated', function ($employee) {
                if ($employee->state_employee == 'Inactivo' && $employee->liquidated)
                    return true;
                else
                    return false;
            })
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
                        
                       if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'png')
                            $fail('Archivo debe ser un pdf, un excel (.xlsx, .xls), un word (.docx, .doc), una presentación (.pptx, .ppt) o una imagen (.jpg, .jpeg, .png)');
                    }
                }

            ],
            "activities.*.documents.*.files.*.name" => [
                function ($attribute, $value, $fail)  use ($request)
                {
                    $index = explode('.', $attribute);

                    $apply = $request->input("activities.$index[1].documents.$index[3].files.$index[5].apply_file");

                    if ($value && is_string($value))
                    {
                        $exist = strpos($value, '/');

                        if ($exist && $apply == 'SI')
                            $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                        else
                        {
                            $exist = strpos($value, '.');

                            if ($exist && $apply == 'SI')
                                $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                        }
                    }
                }
            ],
            "activities.*.documents.*.files.*.expirationDate" => [
                function ($attribute, $value, $fail) use ($request)
                {
                    $index = explode('.', $attribute);

                    $apply = $request->input("activities.$index[1].documents.$index[3].files.$index[5].required_expiration_date");

                    $isset_id = $request->input("activities.$index[1].documents.$index[3].files.$index[5].id");

                    if (!$isset_id)
                    {
                        if ($apply == 'SI')
                        {
                            if ($index[5] > 0)
                            {
                                $i_file = $index[5]-1;

                                $expired_date_old_file = $request->input("activities.$index[1].documents.$index[3].files.$i_file.expirationDate");

                                $valid = Carbon::parse($value)->gt(Carbon::parse($expired_date_old_file));

                                if (!$valid)
                                    $fail('La fecha de vencimiento del archivo no puede ser igual o menor que la fecha de vencimiento del archivo cargado anteriormente');

                            }
                        }
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
            $employee->income_date = isset($request->income_date) ? (Carbon::createFromFormat('D M d Y',$request->income_date))->format('Ymd') : NULL;

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
                [ 'state' => $documents_complets]
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
            $contractEmployee->income_date = $contractEmployee->income_date ? (Carbon::createFromFormat('Y-m-d',$contractEmployee->income_date))->format('D M d Y') : NULL;

            $contractEmployee->multiselect_departament = $contractEmployee->departament ? $contractEmployee->departament->multiselect() : [];

            $contractEmployee->multiselect_city = $contractEmployee->city ? $contractEmployee->city->multiselect() : [];

            $activities = $contractEmployee->activities->transform(function($activity, $index) use ($contractEmployee) {
                $activity->key = Carbon::now()->timestamp + rand(1,10000);
                $activity->name = $activity->name;
                $activity->selected = $activity->id;
                $activity->multiselect_activity = $activity->multiselect();
                $activity->documents = $this->getFilesByActivity($activity->id, $contractEmployee->id, $contractEmployee->contract_id);

                return $activity;
            });

            $contractEmployee->activities = $activities;

            $contractEmployee->type_file = $contractEmployee->file_inactivation ? explode('.',$contractEmployee->file_inactivation)[1] : NULL;
            $contractEmployee->file_inactivation_path = $contractEmployee->file_inactivation ? Storage::disk('s3')->url('legalAspects/files/'. $contractEmployee->file_inactivation) : NULL;

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

            $contractEmployee->load([
                'observations' => function ($query) {
                    $query->with('madeBy')->orderBy('created_at', 'desc');
                }
            ]);

            $contractEmployee->new_observations = '';

            $oldObservations = [];

            foreach ($contractEmployee->observations as $observation)
            {
                array_push($oldObservations, [
                    'id' => $observation->id,
                    'description' => $observation->description,
                    'made_by' => $observation->madeBy ? $observation->madeBy->name :'',
                    'updated_at' => Carbon::parse($observation->updated_at)->format('d-m-Y H:m:s')
                ]);
            }

            $contractEmployee->old_observations = $oldObservations;

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

                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'png')
                            $fail('Archivo debe ser un pdf, un excel (.xlsx, .xls), un word (.docx, .doc), una presentación (.pptx, .ppt) o una imagen (.jpg, .jpeg, .png)');
                    }
                }

            ],
            "activities.*.documents.*.files.*.expirationDate" => [
                function ($attribute, $value, $fail) use ($request)
                {
                    $index = explode('.', $attribute);

                    $apply = $request->input("activities.$index[1].documents.$index[3].files.$index[5].required_expiration_date");

                    $isset_id = $request->input("activities.$index[1].documents.$index[3].files.$index[5].id");

                    if (!$isset_id)
                    {
                        if ($apply == 'SI')
                        {
                            if ($index[5] > 0)
                            {
                                $i_file = $index[5]-1;

                                $expired_date_old_file = $request->input("activities.$index[1].documents.$index[3].files.$i_file.expirationDate");

                                $valid = Carbon::parse($value)->gt(Carbon::parse($expired_date_old_file));

                                if (!$valid)
                                    $fail('La fecha de vencimiento del archivo no puede ser igual o menor que la fecha de vencimiento del archivo cargado anteriormente');

                            }
                        }
                    }
                }
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employeeContract->fill($request->all());
            $employeeContract->date_of_birth = isset($request->date_of_birth) ? (Carbon::createFromFormat('D M d Y',$request->date_of_birth))->format('Ymd') : NULL;
            $employeeContract->income_date = isset($request->income_date) ? (Carbon::createFromFormat('D M d Y',$request->income_date))->format('Ymd') : NULL;

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
                [ 'state' => $documents_complets]
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
                        //Storage::disk('s3')->delete('legalAspects/files/'. $path);
                    }
                }
            }

            $this->saveLogActivitySystem('Contratistas - Empleados', 'Se edito el empleado '.$employeeContract->name.' - '.$employeeContract->identification);

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

        $class_documents = $this->getFilesActivities($employee, $activitiesList);

        foreach ($activitiesList as $activity)
        {
            $activities->push($activity['selected']);
            $files_id = [];

            foreach ($activity['documents'] as $document)
            {
                $apply = $document['apply_file'] == 'SI' ? true : false;
                $class = $document['class'];
                $fileClassTotal = $class_documents[$class];

                foreach ($fileClassTotal as $key => $value) 
                {
                    //// Esto se hace para cargar el mismo documento en todas las clases que se repitan en las actividades asignadas, siempre y cuando el archivo sea nuevo.
                    if (!isset($value['id']) && $value['activity'] != $document['activity_id'])
                        array_push($document['files'], $value);
                }

                if (COUNT($document['files']) > 0 && $apply)
                {
                    $files_names_delete = [];

                    foreach ($document['files'] as $keyF => $file) 
                    {
                        $create_file = false;

                        if (isset($file['id']))
                        {
                            $fileUpload = FileUpload::findOrFail($file['id']);

                            if ($file['old_name'] != $file['file'])
                                $create_file = true;
                            /*else
                                array_push($files_names_delete, $file['old_name']);*/
                        }
                        else
                        {
                            $create_file = true;

                            $fileUpload = new FileUpload();
                            $fileUpload->user_id = $this->user->id;
                        }

                        if ($create_file)
                        {
                            if (!isset($file['has_class']))
                            {
                                $file_tmp = $file['file'];
                                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
                                $file_tmp->storeAs('legalAspects/files/', $nameFile, 's3');

                                $fileUpload->file = $nameFile;
                                $fileUpload->name = $file['name'];
                                $fileUpload->expirationDate = isset($file['required_expiration_date']) && $file['required_expiration_date'] == 'SI' ? ($file['expirationDate'] == null ? null : (Carbon::parse($file['expirationDate']))->format('Ymd')) : null;
                            }
                            else
                            {
                                $fileUpload->file = $file['file'];
                                $fileUpload->name = $file['name'];
                                $fileUpload->expirationDate = $file['expirationDate'];
                            }
                        }
                        else
                        {
                            $fileUpload->name = $file['name'];
                            $fileUpload->expirationDate = isset($file['required_expiration_date']) && $file['required_expiration_date'] == 'SI' ? ($file['expirationDate'] == null ? null : (Carbon::parse($file['expirationDate']))->format('Ymd')) : null;
    
                        }

                        $fileUpload->module = 'Empleados';

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
                    /*foreach ($files_names_delete as $keyf => $file)
                    {
                        Storage::disk('s3')->delete('legalAspects/files/'. $file);
                    }*/
                }
                else if (!$apply)
                {
                    if (COUNT($document['files']) > 0)
                    {
                        foreach ($document['files'] as $keyF => $file) 
                        {
                            if (isset($file['id']))
                            {
                                $fileUpload = FileUpload::findOrFail($file['id']);

                                if ($file['old_name'] == $file['file'])
                                    $create_file = false;
                            }
                            else
                            {
                                $fileUpload = new FileUpload();
                                $fileUpload->user_id = $this->user->id;
                            }

                            $fileUpload->name = $file['name'];
                            $fileUpload->file = $fileUpload->file;
                            $fileUpload->expirationDate = null;

                            $fileUpload->apply_file = $document['apply_file'];
                            $fileUpload->apply_motive = $document['apply_motive'];
                            $fileUpload->module = 'Empleados';

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
                    }
                    else
                    {
                        $fileUpload = new FileUpload();
                        $fileUpload->user_id = $this->user->id;
                        $fileUpload->name = $document['name'].' no aplica';
                        $fileUpload->file = $document['name'].' no aplica '.(Carbon::now()->timestamp + rand(1,10000));
                        $fileUpload->expirationDate = null;
                        $fileUpload->apply_file = $document['apply_file'];
                        $fileUpload->apply_motive = $document['apply_motive'];

                        if (!$fileUpload->save())
                            return $this->respondHttp500();

                        $state = new FileModuleState;
                        $state->contract_id = $employee->contract_id;
                        $state->file_id = $fileUpload->id;
                        $state->module = 'Empleados';
                        $state->state = 'CREADO';                            
                        $state->date = date('Y-m-d');
                        $state->save();
                        
                        $key_file = Carbon::now()->timestamp + rand(1,10000);
                        $fileUpload->contracts()->sync([$employee->contract_id]);
                        $ids = [];
                        $ids[$document['id']] = ['employee_id' => $employee->id];
                        $files_id[$key_file] = $fileUpload->id;
                        $fileUpload->documents()->sync($ids);
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

    public function getFilesActivities($employee, $activitiesList)
    {
        $class_document_files = [];

        foreach ($activitiesList as $activity)
        {
            foreach ($activity['documents'] as $document)
            {
                $apply = $document['apply_file'] == 'SI' ? true : false;
                $class = $document['class'];
                $class_document_files[$class] = isset($class_document_files[$class]) ? $class_document_files[$class] : [];

                if (COUNT($document['files']) > 0 && $apply)
                {
                    foreach ($document['files'] as $keyF => $file) 
                    {
                        $create_file = true;

                        if (isset($file['id']))
                        {
                            $fileUpload = FileUpload::findOrFail($file['id']);

                            if ($file['old_name'] == $file['file'])
                                $create_file = false;
                        }

                        if ($create_file)
                        {
                            $file_tmp = $file['file'];
                            $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
                            $file_tmp->storeAs('legalAspects/files/', $nameFile, 's3');

                            $content = [
                                'has_class' => true,
                                'create_file' => true,
                                'activity' => $document['activity_id'],
                                'key' => Carbon::now()->timestamp + rand(1,10000),
                                'name' => $file['name'],
                                'file' => $nameFile,
                                'expirationDate' => isset($file['required_expiration_date']) && $file['required_expiration_date'] == 'SI' ? ($file['expirationDate'] == null ? null : (Carbon::parse($file['expirationDate']))->format('Ymd')) : null,
                            ];

                            array_push($class_document_files[$class], $content);
                        }
                        else
                        {
                            $content = [
                                'id' => $file['id'],
                                'has_class' => false,
                                'create_file' => false,
                                'activity' => $document['activity_id'],
                                'key' => Carbon::now()->timestamp + rand(1,10000),
                                'name' => $file['name'],
                                'file' => $file['file'],
                                'expirationDate' => isset($file['required_expiration_date']) && $file['required_expiration_date'] == 'SI' ? ($file['expirationDate'] == null ? null : (Carbon::parse($file['expirationDate']))->format('Ymd')) : null,
                            ];
                            
                            array_push($class_document_files[$class], $content);
                        }
                    }
                }
            }
        }

        return $class_document_files;
    }

    public function documentscomplets($employee, $activitiesList, $documents)
    {
        foreach ($activitiesList as $activity)
        {
            $documents_counts = COUNT($activity['documents']);
            $count = 0;

            $rejected = false;
            $pending = false;
            $expired = false;

            foreach ($activity['documents'] as $document)
            {
                $apply = $document['apply_file'] == 'SI' ? true : false;
                $count_aprobe = 0;
                $file_counts = COUNT($document['files']);

                if ($apply && $file_counts > 0 && COUNT($documents) > 0)
                {
                    foreach ($document['files'] as $key => $file) 
                    {
                        if (isset($documents[$file['key']]))
                        {
                            $fileUpload = FileUpload::findOrFail($documents[$file['key']]);

                            if ($fileUpload->expirationDate)
                            {
                                if($fileUpload->expirationDate > date('Y-m-d'))
                                {
                                    if ($fileUpload->state == 'ACEPTADO')
                                    {
                                        $count_aprobe++;
                                        $rejected = false;
                                        $pending = false;
                                        $expired = false;
                                    }
                                    else if ($fileUpload->state == 'RECHAZADO')
                                    {
                                        $rejected = true;
                                        $pending = false;
                                        $expired = false;
                                    }
                                    else if ($fileUpload->state == 'PENDIENTE')
                                    {
                                        $rejected = false;
                                        $pending = true;
                                        $expired = false;
                                    }
                                }
                                else
                                {
                                    if ($fileUpload->state == 'RECHAZADO')
                                    {
                                        $rejected = true;
                                        $pendiente = false;
                                        $expired = true;
                                    }
                                    else if ($fileUpload->state == 'PENDIENTE')
                                    {
                                        $rejected = false;
                                        $pendiente = true;
                                        $expired = true;
                                    }
                                }
                            }
                            else if (!$fileUpload->expirationDate)
                            {
                                if ($fileUpload->state == 'ACEPTADO')
                                {
                                    $count_aprobe++;
                                    $rejected = false;
                                    $pending = false;
                                    $expired = false;
                                }
                                else if ($fileUpload->state == 'RECHAZADO')
                                {
                                    $rejected = true;
                                    $pending = false;
                                    $expired = false;
                                }
                                else if ($fileUpload->state == 'PENDIENTE')
                                {
                                    $rejected = false;
                                    $pending = true;
                                    $expired = false;
                                }
                            }
                        }
                    }
                }
                else if (!$apply)
                {
                    if (COUNT($document['files']) > 0)
                    {
                        foreach ($document['files'] as $key => $file) 
                        {
                            if (isset($documents[$file['key']]))
                            {
                                $fileUpload = FileUpload::findOrFail($documents[$file['key']]);

                                if ($fileUpload->state == 'ACEPTADO')
                                {
                                    $count_aprobe++;
                                    $rejected = false;
                                    $pending = false;
                                }
                                else if ($fileUpload->state == 'RECHAZADO')
                                {
                                    $rejected = true;
                                    $pending = false;
                                }
                                else if ($fileUpload->state == 'PENDIENTE')
                                {
                                    $rejected = false;
                                    $pending = true;
                                }
                            }
                        }
                    }
                }

                if ($count_aprobe == $file_counts && $file_counts > 0)
                    $count++;

                
                if ($rejected)
                    return 'Rechazado';
                else if ($pending || $expired)
                    return 'Pendiente';
            }

            if ($documents_counts > $count)
                return 'Pendiente';
        }

        return 'Aprobado';
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
                    //Storage::disk('s3')->delete('legalAspects/files/'. $path);
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

        if ($request->has('contract_id') || $contract)
        {
            if($request->has('keyword'))
            {
                $keyword = "%{$request->keyword}%";

                $activities = ProyectContract::select("id", "name")
                    ->join('sau_ct_contracts_proyects', 'sau_ct_proyects.id', 'sau_ct_contracts_proyects.proyect_id');

                if ($request->has('contract_id'))
                    $activities->where('sau_ct_contracts_proyects.contract_id', $request->contract_id);
                else
                    $activities->where('sau_ct_contracts_proyects.contract_id', $contract->id);

                $activities = $activities->where(function ($query) use ($keyword) {
                        $query->orWhere('sau_ct_proyects.name', 'like', $keyword);
                    })
                    ->where('company_id', $this->company)
                    ->orderBy('name')
                    ->take(30)->pluck('id', 'name');

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($activities)
                ]);
            }
            else
            {
                $activities = ProyectContract::selectRaw("id, name")->where('company_id', $this->company);

                if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
                {
                    $activities->join('sau_ct_contracts_proyects', 'sau_ct_proyects.id','sau_ct_contracts_proyects.proyect_id' )->where('sau_ct_contracts_proyects.contract_id', $contract->id);
                }

                $activities = $activities->orderBy('name')->pluck('id', 'name');
            
                return $this->multiSelectFormat($activities);
            }
        }
        else
        {
            $proyects = ProyectContract::selectRaw("
                sau_ct_proyects.id as id,
                sau_ct_proyects.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($proyects);
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
                    'sau_ct_file_upload_contracts_leesse.reason_rejection AS reason_rejection',
                    'sau_ct_file_upload_contracts_leesse.apply_file AS apply_file',
                    'sau_ct_file_upload_contracts_leesse.apply_motive AS apply_motive',
                    'sau_ct_file_upload_contracts_leesse.observations AS observations',
                    'sau_ct_file_upload_contracts_leesse.created_at AS created_at'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract)
                ->where('sau_ct_file_document_employee.document_id', $document->id)
                ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                ->whereRaw("sau_ct_file_upload_contracts_leesse.name <> '' and sau_ct_file_upload_contracts_leesse.file <> ''")
                ->get();

                if ($files)
                {
                    $apply_file = 'SI';
                    $apply_motive = '';
                    $state_file = '';
                    $motive_rejected_file = '';
                    $files->transform(function($file, $index) use (&$apply_file, &$apply_motive, &$state_file, &$motive_rejected_file){

                        $explode = explode('.',$file->file);
                        $type = $file->file && COUNT($explode) > 1 ? $explode[1] : null;

                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->old_name = $file->file;
                        $file->file = $file->file;
                        $file->type = $type;
                        $file->path = Storage::disk('s3')->url('legalAspects/files/'. $file->file);
                        $file->observations = $file->observations;
                        $file->expirationDate = $file->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$file->expirationDate))->format('D M d Y');
                        $file->required_expiration_date = $file->expirationDate == null ? 'NO' : 'SI';
                        $file->state = $file->state;
                        $file->reason_rejection = $file->reason_rejection;
                        $file->apply_file = $file->apply_file;
                        $file->apply_motive = $file->apply_motive;
                        $file->edit_document = false;

                        $apply_motive = $file->apply_motive;
                        $apply_file = $file->apply_file;
                        $state_file = $file->state;
                        $motive_rejected_file = $file->reason_rejection;

                        return $file;
                    });


                    $document->apply_file = $apply_file;
                    $document->apply_motive = $apply_motive;
                    $document->state_file = $state_file;
                    $document->motive_rejected_file = $motive_rejected_file;
                    $document->files = $files;
                }

                $document->required_expired_date = $document->required_expired_date;

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

    public function download($id)
    {
        $employeeContract = ContractEmployee::find($id);

        if (Storage::disk('s3')->exists('legalAspects/files/'. $employeeContract->file_inactivation)) 
        {
            return Storage::disk('s3')->download('legalAspects/files/'. $employeeContract->file_inactivation);
        }

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

    public function toggleState(ContractEmployeeInactiveRequest $request)
    {
        try
        {
            $employeeContract = ContractEmployee::find($request->id);

            if ($employeeContract->state_employee)
            {
                $data = [
                    'state_employee' => !$employeeContract->state_employee,
                    'deadline' => (Carbon::createFromFormat('D M d Y',$request->deadline))->format('Ymd'),
                    'motive_inactivation' => $request->motive_inactivation
                ];
            }
            else
            {
                $data = [
                    'state_employee' => !$employeeContract->state_employee,
                    'deadline' => NULL,
                    'motive_inactivation' => NULL,
                    'liquidated' => false,
                    'liquidated_date' => NULL,
                    'file_inactivation' => NULL
                ];
            }

            if (!$employeeContract->update($data)) {
                return $this->respondHttp500();
            }
            return $this->respondHttp200([
                'message' => 'Se cambio el estado del empleado'
            ]);

        } catch(Exception $e) {   
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    public function toggleLiqudated(ContractEmployeeLiquidatedRequest $request)
    {
        try
        {
            $employeeContract = ContractEmployee::find($request->id);
            $nameFile = NULL;

            if (!$employeeContract->state_employee)
            {
                if ($request->file_inactivation)
                {
                    $file_tmp = $request->file_inactivation;
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->getClientOriginalExtension();
                    $file_tmp->storeAs('legalAspects/files/', $nameFile, 's3');
                }

                $data = [
                    'liquidated' => true,
                    'liquidated_date' => (Carbon::createFromFormat('D M d Y',$request->liquidated_date))->format('Ymd'),
                    'file_inactivation' => $nameFile
                ];

                if (!$employeeContract->update($data)) {
                    return $this->respondHttp500();
                }
            }

            return $this->respondHttp200([
                'message' => 'Se liquido el empleado'
            ]);

        } catch(Exception $e) {   
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    public function multiselectEmployee(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $employees = ContractEmployee::select("id", "name")
                ->where('sau_ct_contract_employees.contract_id', $request->contract_id)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_ct_contract_employees.name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');        

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($employees)
            ]);
        }
        else
        {
            $employees = ContractEmployee::selectRaw("id, name")
            ->where('sau_ct_contract_employees.contract_id', $request->contract_id)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($employees);
        }
    }

    public function multiselectEmployeeDocuments(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";

            $employees = FileUpload::select("sau_ct_file_upload_contracts_leesse.id", "sau_ct_file_upload_contracts_leesse.name")
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_file_document_employee.employee_id')
                ->where('sau_ct_contract_employees.contract_id', $request->contract_id)
                ->where('sau_ct_contract_employees.id', $request->employee_id)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_ct_contract_employees.name', 'like', $keyword);
                })
                ->where('sau_ct_file_upload_contracts_leesse.file', 'like', '%.pdf%')
                ->orderBy('name')
                ->take(30)
                ->pluck('id', 'name');


            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($employees)
            ]);
        }
        else
        {
            $employees = FileUpload::selectRaw("sau_ct_file_upload_contracts_leesse.id, sau_ct_file_upload_contracts_leesse.name")
                ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.id', 'sau_ct_file_document_employee.employee_id')
                ->where('sau_ct_contract_employees.contract_id', $request->contract_id)
                ->where('sau_ct_contract_employees.id', $request->employee_id)
                ->where('sau_ct_file_upload_contracts_leesse.file', 'like', '%.pdf%')
                ->orderBy('name')
                ->pluck('id', 'name');
        
            return $this->multiSelectFormat($employees);
        }
    }

    public function downloadMerge(Request $request)
    {
        $pdfResult = PDFMerger::init();//new PdfManage;
        $pathPresentation = '';
        $files_ids = $this->getValuesForMultiselect($request->documents_id);

        $employee = ContractEmployee::find($request->employee_id);

        $files = FileUpload::whereIn('id', $files_ids)->get();

        $files_temp = [];

        foreach ($files as $key => $file)
        {
            $file_temp = storage_path('app/temp/').$file->file;

            file_put_contents(
                $file_temp,
                file_get_contents( Storage::disk('s3')->url("legalAspects/files/{$file->file}") )
            );

            $files_temp[] = $file_temp;

            $pdfResult->addPDF($file_temp, 'all', 'L');
        }

        $nameFilePDF = time()."_{$employee->name}.pdf";
        $pdfResult->merge();
        $pdfResult->save(storage_path('app/temp/').$nameFilePDF, "file");

        foreach ($files_temp as $temp)
        {
            File::delete($temp);
        }

        return response()->download(storage_path('app/temp/').$nameFilePDF, $nameFilePDF, [
            "Content-Type" => "application/pdf",
            "file-name" => $nameFilePDF
        ])
        ->deleteFileAfterSend(true);

        ob_end_clean();
    }

    public function downloadTemplateInactiveImport()
    {
      return Excel::download(new EmployeeInactiveTemplate(collect([]), $this->company), 'PlantillaSeguridadSocialEmpleados.xlsx');
    }

    public function importSocialSecure(Request $request)
    {
        Validator::make($request->all(), [
            "file_employee" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                       if ($ext != 'xlsx' && $ext != 'xls')
                            $fail('Archivo debe ser un excel (.xlsx, .xls)');
                    }
                    else if (!$value)
                        $fail('Archivo debe ser un excel (.xlsx, .xls)');
                }
            ],
            "file_social_secure" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                       if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'png')
                            $fail('Archivo debe ser un pdf, un excel (.xlsx, .xls), un word (.docx, .doc), una presentación (.pptx, .ppt) o una imagen (.jpg, .jpeg, .png)');
                    }
                    else if (!$value)
                        $fail('Archivo debe ser un excel (.xlsx, .xls)');
                }
            ],
            "description" => [
                function ($attribute, $value, $fail)
                {
                    if (!$value || !is_string($value))
                    {
                        $fail('El campo descripcion es obligatorio');
                    }
                }
            ]
        ])->validate();

      try
      {
        $contract = $this->getContractUser($this->user->id, $this->company);

        $file_tmp = $request->file_social_secure;
        $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->getClientOriginalExtension();
        $file_tmp->storeAs('legalAspects/files/', $nameFile, 's3');
        $file_social_secure = $nameFile;

        //$path_file_employee = 

        ContractEmployeeImportSocialSecureJob::dispatch($request->file_employee, $this->company, $this->user, $contract, $request->description, $file_social_secure);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
          \Log::info($e->getMessage());
        return $this->respondHttp500();
      }
    }

    public function reportDocumentPending(Request $request)
    {
        $documentsEmployee = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.id AS id',
                'sau_ct_information_contract_lessee.social_reason AS contract',
                'sau_ct_contract_employees.name AS employee',
                'sau_ct_activities.name AS activity',
                'sau_ct_activities_documents.name AS document',
                DB::raw("case when sau_ct_file_document_employee.employee_id is not null then 'SI' else 'NO' end AS cargado"),
                DB::raw("GROUP_CONCAT(CONCAT(' ', sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) AS proyects")
            )
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->leftJoin('sau_ct_activities_documents', function ($join)
            {
                $join->on("sau_ct_activities_documents.activity_id", 'sau_ct_activities.id');
                $join->where('sau_ct_activities_documents.type', 'Empleado');
            })
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            })
            ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
            ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
            ->whereNull('sau_ct_file_document_employee.employee_id')
            ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.name', 'sau_ct_activities.name', 'sau_ct_activities_documents.name', 'sau_ct_file_document_employee.employee_id');
            

        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsEmployee->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsEmployee->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function reportDocumentPendingExpired(Request $request)
    {
        $documentsEmployee = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.id AS id',
                'sau_ct_information_contract_lessee.social_reason AS contract',
                'sau_ct_contract_employees.name AS employee',
                'sau_ct_activities.name AS activity',
                'sau_ct_activities_documents.name AS document',
                DB::raw("GROUP_CONCAT(CONCAT(' ', sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) AS proyects"),
                'sau_ct_file_upload_contracts_leesse.expirationDate'
            )
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->join('sau_ct_activities_documents', 'sau_ct_activities_documents.activity_id', 'sau_ct_activities.id')
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            })
            ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
            ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
            ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_employee.file_id')
            ->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate < curdate()")
            ->whereNotNull('sau_ct_file_upload_contracts_leesse.expirationDate')
            ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.name', 'sau_ct_activities.name', 'sau_ct_activities_documents.name', 'sau_ct_file_document_employee.employee_id', 'sau_ct_file_upload_contracts_leesse.expirationDate');
            

        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsEmployee->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsEmployee->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function globalDocument(Request $request)
    {
        $documentsGlobal = ContractLesseeInformation::select(
            'sau_ct_information_contract_lessee.id as id',
            'sau_ct_contracts_documents.name as documento',
            'sau_ct_information_contract_lessee.social_reason AS contratista',
            DB::raw("case when sau_ct_contracts_documents.document_id is not null then sau_ct_activities.name else 'Documentos Globales' end AS activity")
        )
        ->join('sau_ct_contracts_documents', 'sau_ct_information_contract_lessee.company_id', 'sau_ct_contracts_documents.company_id')
        ->leftJoin('sau_ct_file_document_contract', function ($join) 
        {
            $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
            $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
        })
        ->leftJoin('sau_ct_contracts_activities', 'sau_ct_contracts_activities.contract_id', 'sau_ct_information_contract_lessee.id')
        ->leftJoin('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contracts_activities.activity_id')
        ->leftJoin('sau_ct_activities_documents', function ($join)
        {
            $join->on("sau_ct_activities_documents.activity_id", 'sau_ct_activities.id');
            $join->on('sau_ct_contracts_activities.activity_id', "sau_ct_activities_documents.activity_id");
            $join->where('sau_ct_activities_documents.type', 'Contratista');
        })
        ->whereNull('sau_ct_file_document_contract.contract_id')
        ->whereRaw("(sau_ct_contracts_documents.document_id is null OR sau_ct_activities_documents.id = sau_ct_contracts_documents.document_id) and sau_ct_information_contract_lessee.company_id = {$this->company} and sau_ct_contracts_documents.company_id = {$this->company}")
        ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contracts_documents.name', 'sau_ct_contracts_documents.id', 'sau_ct_contracts_documents.document_id', 'activity');
        
        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsGlobal->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsGlobal->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsGlobal)
                    ->make();
    }

    public function globalDocumentExpired(Request $request)
    {
        $documentsGlobal = ContractLesseeInformation::select(
            'sau_ct_information_contract_lessee.id as id',
            'sau_ct_contracts_documents.name as documento',
            'sau_ct_information_contract_lessee.social_reason AS contratista',
            DB::raw("case when sau_ct_contracts_documents.document_id is not null then sau_ct_activities.name else 'Documentos Globales' end AS activity"),
            'sau_ct_file_upload_contracts_leesse.expirationDate'
        )
        ->join('sau_ct_contracts_documents', 'sau_ct_information_contract_lessee.company_id', 'sau_ct_contracts_documents.company_id')
        ->leftJoin('sau_ct_file_document_contract', function ($join) 
        {
            $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
            $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
        })
        ->leftJoin('sau_ct_contracts_activities', 'sau_ct_contracts_activities.contract_id', 'sau_ct_information_contract_lessee.id')
        ->leftJoin('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contracts_activities.activity_id')
        ->leftJoin('sau_ct_activities_documents', function ($join)
        {
            $join->on("sau_ct_activities_documents.activity_id", 'sau_ct_activities.id');
            $join->on('sau_ct_contracts_activities.activity_id', "sau_ct_activities_documents.activity_id");
            $join->where('sau_ct_activities_documents.type', 'Contratista');
        })
        ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_contract.file_id')
        ->whereRaw("(sau_ct_contracts_documents.document_id is null OR sau_ct_activities_documents.id = sau_ct_contracts_documents.document_id) and sau_ct_information_contract_lessee.company_id = {$this->company} and sau_ct_contracts_documents.company_id = {$this->company}")
        ->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate < curdate()")
        ->whereNotNull('sau_ct_file_upload_contracts_leesse.expirationDate')
        ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contracts_documents.name', 'sau_ct_contracts_documents.id', 'sau_ct_contracts_documents.document_id', 'activity', 'sau_ct_file_upload_contracts_leesse.expirationDate');
        
        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsGlobal->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsGlobal->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsGlobal)
                    ->make();
    }

    public function reportDocumentCloseToWinning(Request $request)
    {
        $days = $request->days;

        $documentsEmployee = ContractLesseeInformation::select(
                'sau_ct_information_contract_lessee.id AS id',
                'sau_ct_information_contract_lessee.social_reason AS contract',
                'sau_ct_contract_employees.name AS employee',
                'sau_ct_activities.name AS activity',
                'sau_ct_activities_documents.name AS document',
                DB::raw("GROUP_CONCAT(CONCAT(' ', sau_ct_proyects.name) ORDER BY sau_ct_proyects.name ASC) AS proyects"),
                'sau_ct_file_upload_contracts_leesse.expirationDate'
            )
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->join('sau_ct_activities_documents', 'sau_ct_activities_documents.activity_id', 'sau_ct_activities.id')
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            })
            ->leftJoin('sau_ct_contract_employee_proyects', 'sau_ct_contract_employee_proyects.employee_id', 'sau_ct_contract_employees.id')
            ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_contract_employee_proyects.proyect_contract_id')
            ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_employee.file_id')
            //->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate < curdate()")
            ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -$days DAY)")
            ->whereNotNull('sau_ct_file_upload_contracts_leesse.expirationDate')
            ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contract_employees.name', 'sau_ct_activities.name', 'sau_ct_activities_documents.name', 'sau_ct_file_document_employee.employee_id', 'sau_ct_file_upload_contracts_leesse.expirationDate');
            

        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsEmployee->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsEmployee->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function globalDocumentCloseToWinning(Request $request)
    {
        $days = $request->daysContract;

        $documentsGlobal = ContractLesseeInformation::select(
            'sau_ct_information_contract_lessee.id as id',
            'sau_ct_contracts_documents.name as documento',
            'sau_ct_information_contract_lessee.social_reason AS contratista',
            DB::raw("case when sau_ct_contracts_documents.document_id is not null then sau_ct_activities.name else 'Documentos Globales' end AS activity"),
            'sau_ct_file_upload_contracts_leesse.expirationDate'
        )
        ->join('sau_ct_contracts_documents', 'sau_ct_information_contract_lessee.company_id', 'sau_ct_contracts_documents.company_id')
        ->leftJoin('sau_ct_file_document_contract', function ($join) 
        {
            $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
            $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
        })
        ->leftJoin('sau_ct_contracts_activities', 'sau_ct_contracts_activities.contract_id', 'sau_ct_information_contract_lessee.id')
        ->leftJoin('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contracts_activities.activity_id')
        ->leftJoin('sau_ct_activities_documents', function ($join)
        {
            $join->on("sau_ct_activities_documents.activity_id", 'sau_ct_activities.id');
            $join->on('sau_ct_contracts_activities.activity_id', "sau_ct_activities_documents.activity_id");
            $join->where('sau_ct_activities_documents.type', 'Contratista');
        })
        ->join('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_contract.file_id')
        ->whereRaw("(sau_ct_contracts_documents.document_id is null OR sau_ct_activities_documents.id = sau_ct_contracts_documents.document_id) and sau_ct_information_contract_lessee.company_id = {$this->company} and sau_ct_contracts_documents.company_id = {$this->company}")
        //->whereRaw("sau_ct_file_upload_contracts_leesse.expirationDate < curdate()")
        ->whereRaw("CURDATE() = DATE_ADD(sau_ct_file_upload_contracts_leesse.expirationDate, INTERVAL -$days DAY)")
        ->whereNotNull('sau_ct_file_upload_contracts_leesse.expirationDate')
        ->groupBy('sau_ct_information_contract_lessee.id', 'sau_ct_contracts_documents.name', 'sau_ct_contracts_documents.id', 'sau_ct_contracts_documents.document_id', 'activity', 'sau_ct_file_upload_contracts_leesse.expirationDate');
        
        /*$filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $documentsGlobal->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsGlobal->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }*/

        return Vuetable::of($documentsGlobal)
                    ->make();
    }

    public function consultingDocumentResumen(Request $request)
    {
        try {
            $employee = ContractEmployee::find($request->employee_id);

            $contract = ContractLesseeInformation::find($employee->contract_id);

            $activities = $employee->activities->transform(function($activity, $index) use ($employee) {
                $activity->documents = $this->getFilesByActivity($activity->id, $employee->id, $employee->contract_id);

                return $activity;
            });

            foreach ($activities as $key => $activity) 
            {
                foreach ($activity->documents as $key2 => $document) 
                {
                    if (COUNT($document->files) > 0)
                    {
                        $files = $document->files;

                        $files = $files->sortByDesc('id')->first();
                
                        $files->expirationDate = $files->expirationDate ? (Carbon::createFromFormat('D M d Y', $files->expirationDate)->format('Y-m-d')) : 'No aplica';

                        $document->files = $files;
                    }
                }
            }

            $data = [
                'contract' => $contract->social_reason,
                'contract_id' => $contract->id,
                'employee' => $employee->name,
                'activities' => $activities->values()
            ];

            return $this->respondHttp200([
                'data' => $data
            ]);

        }  catch(Exception $e) {
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    public function getDaySocialSecurityExpired(Request $request)
    {
        if ($request->contract_id && $request->contract_id > 0)
            $contract = ContractLesseeInformation::find($request->contract_id);
        else
            $contract = ContractLesseeInformation::find(Session::get('contract_id'));
        
        $dateExpired = $this->calculateDaySocialSecurityExpired($contract);

        return Carbon::createFromFormat('Y-m-d',$dateExpired)->format('D M d Y');
    }
}
