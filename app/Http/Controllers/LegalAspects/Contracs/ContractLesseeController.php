<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Administrative\Roles\Role;
use App\Models\LegalAspects\Contracts\CompanyLimitCreated;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\Qualifications;;
use App\Models\LegalAspects\Contracts\HighRiskType;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Http\Requests\LegalAspects\Contracts\ContractRequest;
use App\Http\Requests\LegalAspects\Contracts\ListCheckItemsRequest;
use App\Jobs\LegalAspects\Contracts\ListCheck\ListCheckContractExportJob;
use App\Jobs\LegalAspects\Contracts\Contractor\ContractorExportJob;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use Carbon\Carbon;
use Validator;
use DB;

class ContractLesseeController extends Controller
{
    use UserTrait;
    use ContractTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_r, {$this->team}", ['except' => ['getInformation', 'multiselect', 'getListCheckItems', 'qualifications', 'saveQualificationItems', 'update']] );
        $this->middleware("permission:contracts_u|contracts_myInformation, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_myInformation, {$this->team}", ['only' => 'getInformation']);
        $this->middleware("permission:contracts_export, {$this->team}", ['only' => 'export']);
    }

    /**
     * Display index.
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
        $contracts = ContractLesseeInformation::select(
                    'sau_ct_information_contract_lessee.*',
                    'sau_ct_list_check_resumen.total_standard AS total_standard',
                    'sau_ct_list_check_resumen.total_c AS total_c',
                    'sau_ct_list_check_resumen.total_nc AS total_nc',
                    'sau_ct_list_check_resumen.total_sc AS total_sc',
                    'sau_ct_list_check_resumen.total_p_c AS total_p_c',
                    'sau_ct_list_check_resumen.total_p_nc AS total_p_nc'
                    )
                    ->leftJoin('sau_ct_list_check_resumen', 'sau_ct_list_check_resumen.contract_id', 'sau_ct_information_contract_lessee.id');

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $contracts->rangePercentageCumple($filters["rangePC"]);
        }

        return Vuetable::of($contracts)
            ->addColumn('legalaspects-contracts-view-list-check', function ($contract) {
                
                if ($contract->type == 'Contratista')
                    return true;

                return false;
            })
            ->make();
    }

    public function store(ContractRequest $request)
    {   
        DB::beginTransaction();

        try
        {
            if (!$this->checkLimit())
                return $this->respondWithError('Límite alcanzado..!! No puede crear más contratistas o arrendatarios hasta que inhabilite alguno de ellos.');

            $contract = new ContractLesseeInformation($request->all());
            $contract->company_id = $this->company;

            if (!$contract->save())
                return $this->respondHttp500();

            $risks = ($request->high_risk_work == 'SI') ? $this->getDataFromMultiselect($request->high_risk_type_id) : [];
            $contract->highRiskType()->sync($risks);

            $user = $this->createUser($request);

            if ($user == $this->respondHttp500() || $user == null) {
                return $this->respondHttp500();
            }

            $user->attachRole($this->getIdRole($request->type), $this->company);
            $contract->users()->sync($user);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la contratista'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $contract = ContractLesseeInformation::findOrFail($id);

            $high_risk_type_id = [];

            foreach ($contract->highRiskType as $key => $value)
            {                
                array_push($high_risk_type_id, $value->multiselect());
            }

            $contract->multiselect_high_risk_type = $high_risk_type_id;
            $contract->high_risk_type_id = $high_risk_type_id;

            return $this->respondHttp200([
                'data' => $contract,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInformation()
    {
        try
        {
            $contract = $this->getContractUser($this->user->id);
            $contract->isInformation = true;

            return $this->respondHttp200([
                'data' => $contract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\LegalAspects\Contracts\ContractRequest $request
     * @param App\Models\LegalAspects\Contracts\ContractLesseeInformation $typeRating
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, ContractLesseeInformation $contract)
    {
        DB::beginTransaction();

        try
        {
            if ($request->active == 'SI' && ($request->active != $contract->active))
            {
                if (!$this->checkLimit())
                    return $this->respondWithError('Límite alcanzado..!! No puede habilitar esta contratista o arrendatario hasta que inhabilite alguno de ellos.');
            }

            $contract->fill($request->all());
            
            if ($request->type == 'Arrendatario')
                $contract->classification = NULL;

            if ($request->has('isInformation'))
                $contract->completed_registration = 'SI';            
            else
            {
                $risks = ($request->high_risk_work == 'SI') ? $this->getDataFromMultiselect($request->high_risk_type_id) : [];
                $contract->highRiskType()->sync($risks);
            }

            if (!$contract->update())
                return $this->respondHttp500();

            $users = $this->getUsersContract($contract->id);

            foreach ($users as $user)
            {
                $user->syncRoles([$this->getIdRole($contract->type)], $this->company);

                /*if ($contract->active == 'NO')
                {
                    $user->active = 'NO';
                    $user->save();
                }*/
            }

            $this->reloadLiskCheckResumen($contract);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //return $e->getMessage();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la contratista'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $contracts = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id as id,
                CONCAT(sau_ct_information_contract_lessee.nit, ' - ',sau_ct_information_contract_lessee.social_reason) as nit
            ")
            ->where(function ($query) use ($keyword) {
                $query->orWhere('nit', 'like', $keyword);
            })
            ->take(30)->pluck('id', 'nit');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($contracts)
            ]);
        }
        else
        {
            $contracts = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id as id,
                CONCAT(sau_ct_information_contract_lessee.nit, ' - ',sau_ct_information_contract_lessee.social_reason) as nit
            ")->pluck('id', 'nit');
        
            return $this->multiSelectFormat($contracts);
        }
    }

    private function getIdRole($role)
    {
        $role = Role::defined()
            ->where('name', $role)
            ->first();

        if ($role)
            $role = $role->id;

        return $role;
    }

    private function checkLimit()
    {
        $limit = CompanyLimitCreated::select('value')->first();

        if ($limit)
            $limit = $limit->value;
        else 
            $limit = 6;

        $count_contracts = ContractLesseeInformation::where('active', 'SI')->count();

        if ($count_contracts < $limit)
            return true;

        return false;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getListCheckItems(Request $request)
    {
        try
        {
            if ($request->has('id') && $request->id)
                $contract = ContractLesseeInformation::findOrFail($request->id);
            else 
                $contract = $this->getContractUser($this->user->id);

            $items = $this->getStandardItemsContract($contract);

            $qualifications = Qualifications::pluck("name", "id");

            //Obtiene los items calificados
            $items_calificated = ItemQualificationContractDetail::
                      where('contract_id', $contract->id)
                    ->pluck("qualification_id", "item_id");

            if (COUNT($items) > 0)
            {
                $items->transform(function($item, $index) use ($qualifications, $items_calificated, $contract) {
                    //Añade las actividades definidas de cada item para los planes de acción
                    $item->activities_defined = $item->activities()->pluck("description");
                    $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                    $item->files = [];
                    $item->actionPlan = [
                        "activities" => [],
                        "activitiesRemoved" => []
                    ];

                    if ($item->qualification == 'C')
                    {
                        $files = FileUpload::select(
                                    'sau_ct_file_upload_contracts_leesse.id AS id',
                                    'sau_ct_file_upload_contracts_leesse.name AS name',
                                    'sau_ct_file_upload_contracts_leesse.file AS file',
                                    'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                                )
                                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                                ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                                ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                                ->where('sau_ct_file_item_contract.item_id', $item->id)
                                ->get();

                        if ($files)
                        {
                            $files->transform(function($file, $index) {
                                $file->key = Carbon::now()->timestamp + rand(1,10000);
                                $file->old_name = $file->file;
                                $file->expirationDate = $file->expirationDate == null ? null : (Carbon::createFromFormat('Y-m-d',$file->expirationDate))->format('D M d Y');

                                return $file;
                            });

                            $item->files = $files;
                        }
                    }
                    else if ($item->qualification == 'NC')
                    {
                        $model_activity = ItemQualificationContractDetail::
                                  where('contract_id', $contract->id)
                                ->where('item_id', $item->id)
                                ->first();

                        $item->actionPlan = ActionPlan::model($model_activity)->prepareDataComponent();
                    }

                    return $item;
                });

                $data = [
                    'id' => $contract->id,
                    'items' => $items,
                    'delete' => [
						'files' => []
                    ]
                ];
            }
            else
                $data = [];

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function qualifications()
    {
        $qualifications = Qualifications::pluck("description", "name");
        return $qualifications;
    }

    /**
     * Update the list Check
     *
     * @param App\Http\Requests\LegalAspects\Contracts\ContractRequest $request
     * @param App\Models\LegalAspects\Contracts\ContractLesseeInformation $typeRating
     * @return \Illuminate\Http\Response
     */
    public function saveQualificationItems(ListCheckItemsRequest $request)
    {
        Validator::make($request->all(), [
            "items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            //\Log::info($request->all());
            $data = $request->except(['items', 'files_binary']);

            $qualifications = Qualifications::pluck("id", "name");
            $contract = $this->getContractUser($this->user->id);

            //Se inician los atributos necesarios que seran estaticos para todas las actividades
            // De esta forma se evitar la asignacion innecesaria una y otra vez 
            ActionPlan::
                    user($this->user)
                ->module('contracts')
                ->url(url('/administrative/actionplans'));

            if ($request->has('qualification') && $request->qualification)
            {
                $itemQualification = ItemQualificationContractDetail::updateOrCreate(
                    ['contract_id' => $contract->id, 'item_id' => $request->id], 
                    ['contract_id' => $contract->id, 'item_id' => $request->id, 'qualification_id' => $qualifications[$request->qualification]]);

                if (!$itemQualification->save()) 
                    return $this->respondHttp500();

                //Cumple y solo es aqui donde se cargan archivos
                if ($request->qualification == 'C')
                {
                    if ($request->has('files') && COUNT($request->files) > 0)
                    {
                        $files_names_delete = [];

                        foreach ($request->get('files') as $keyF => $file) 
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
                                $file_tmp->storeAs('legalAspects/files/', $nameFile, 'public');
                                $fileUpload->file = $nameFile;
                                $data['files'][$keyF]['file'] = $nameFile;
                                $data['files'][$keyF]['old_name'] = $nameFile;
                            }

                            $fileUpload->name = $file['name'];
                            $fileUpload->expirationDate = $file['expirationDate'] == null ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd');

                            if (!$fileUpload->save())
                                return $this->respondHttp500();
                                
                            $data['files'][$keyF]['id'] = $fileUpload->id;

                            $fileUpload->contracts()->sync([$contract->id]);
                            $fileUpload->items()->sync([$request->id]);
                        }

                        //Borrar archivos reemplazados
                        foreach ($files_names_delete as $keyf => $file)
                        {
                            Storage::disk('public')->delete('legalAspects/files/'. $file);
                        }
                    }
                }

                /**Planes de acción*/
                ActionPlan::
                        model($itemQualification)
                    ->activities($request->actionPlan)
                    ->save();

                $data['actionPlan'] = ActionPlan::getActivities();
            }

            //Borrar archivos que fueron removidos de los items
            if ($request->has('delete'))
            {
                foreach ($request->delete['files'] as $keyF => $file)
                {
                    $file_delete = FileUpload::find($file['id']);

                    if ($file_delete)
                    {
                        $file_delete->delete();
                        Storage::disk('public')->delete('legalAspects/files/'. $file['old_name']);
                    }
                }
            }

            ActionPlan::sendMail();

            DB::commit();

            return $this->respondHttp200([
                'data' => $data
            ]);

            //$this->sendNotification($contract);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    private function sendNotification($contract)
    {
        ListCheckContractExportJob::dispatch($this->company, $contract);
    }

    public function retrySendMail(ContractLesseeInformation $contract)
    {
        $users = $this->getUsersContract($contract->id);
        
        if ($users->count() > 0)
        {
            if ($this->resendMail($users[0]))
                return $this->respondHttp200([
                    'message' => 'Se reenvio el correo'
                ]);
        }

        return $this->respondHttp500();
    }

    /**
     * Export resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try
        {
            ContractorExportJob::dispatch($this->user, $this->company, $request->all());
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function multiselectHighRisk(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $highrisk = HighRiskType::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($highrisk)
            ]);
        }
        else
        {
            $highrisk = HighRiskType::select(
                'sau_ct_high_risk_types.id as id',
                'sau_ct_high_risk_types.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($highrisk);
        }
    }
}
