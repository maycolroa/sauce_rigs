<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Http\Requests\LegalAspects\Contracts\ContractEmployeeRequest;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
use App\Traits\ContractTrait;
use Carbon\Carbon;
use Validator;
use DB;

class ContractEmployeeController extends Controller
{
    use ContractTrait;
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
            'sau_ct_contract_employees.identification AS identification');

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
                        
                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf')
                            $fail('Archivo debe ser un pdf o un excel');
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
            $employee->token = bcrypt($employee->email.$employee->identification);

            if (!$employee->save())
                return $this->respondHttp500();

            $activities = collect([]);

            if($request->has('activities'))
                $activities = $this->saveActivities($employee, $request->activities);

            $employee->activities()->sync($activities->values());

            TrainingSendNotificationJob::dispatch($this->company, '', $employee->id);

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

            $activities = $contractEmployee->activities->transform(function($activity, $index) use ($contractEmployee) {
                $activity->key = Carbon::now()->timestamp + rand(1,10000);
                $activity->name = $activity->name;
                $activity->selected = $activity->id;
                $activity->multiselect_activity = $activity->multiselect();
                $activity->documents = $this->getFilesByActivity($activity->id, $contractEmployee->id);

                return $activity;
            });

            $contractEmployee->activities = $activities;
            $contractEmployee->delete = [
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $contractEmployee,
            ]);
        } catch(Exception $e){
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

                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf')
                            $fail('Archivo debe ser un pdf o un excel');
                    }
                }

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employeeContract->fill($request->all());

            if (!$employeeContract->token)
                $employeeContract->token = bcrypt($employeeContract->email.$employeeContract->identification);

            if(!$employeeContract->update()){
                return $this->respondHttp500();
            }

            $activities = collect([]);

            if($request->has('activities'))
                $activities = $this->saveActivities($employeeContract, $request->activities);

            $employeeContract->activities()->sync($activities->values());

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
                        $fileUpload->expirationDate = $file['expirationDate'] == null ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd');

                        if (!$fileUpload->save())
                            return $this->respondHttp500();

                        $fileUpload->contracts()->sync([$employee->contract_id]);
                        $ids = [];
                        $ids[$document['id']] = ['employee_id' => $employee->id];
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

        return $activities;
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
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }

    public function getFilesForm(Request $request)
    {
        return $this->respondHttp200([
            'data' => $this->getFilesByActivity($request->activity, $request->employee)
        ]);
    }

    public function getFilesByActivity($activity, $employee_id)
    {
        $documents = ActivityDocument::where('activity_id', $activity)->get();

        if ($documents->count() > 0)
        {
            $contract = $this->getContractUser($this->user->id, $this->company);
            $documents = $documents->transform(function($document, $key) use ($contract, $employee_id) {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
                $document->files = [];

                $files = FileUpload::select(
                            'sau_ct_file_upload_contracts_leesse.id AS id',
                            'sau_ct_file_upload_contracts_leesse.name AS name',
                            'sau_ct_file_upload_contracts_leesse.file AS file',
                            'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                        )
                        ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                        ->join('sau_ct_file_document_employee', 'sau_ct_file_document_employee.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                        ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                        ->where('sau_ct_file_document_employee.document_id', $document->id)
                        ->where('sau_ct_file_document_employee.employee_id', $employee_id)
                        ->get();

                if ($files)
                {
                    $files->transform(function($file, $index) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->old_name = $file->file;
                        $file->expirationDate = $file->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$file->expirationDate))->format('D M d Y');

                        return $file;
                    });

                    $document->files = $files;
                }

                return $document;
            });
        }

        return $documents;
    }
}
