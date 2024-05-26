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
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\HighRiskType;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\ContractDocument;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\ListCheckQualification;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\TagsSocialSecurityPaymentOperator;
use App\Models\LegalAspects\Contracts\TagsIps;
use App\Models\LegalAspects\Contracts\TagsArl;
use App\Models\LegalAspects\Contracts\TagsHeightTrainingCenter;
use App\Models\Administrative\Users\LogUserModify;
use App\Http\Requests\LegalAspects\Contracts\DocumentRequest;
use App\Http\Requests\LegalAspects\Contracts\ContractRequest;
use App\Http\Requests\LegalAspects\Contracts\ListCheckItemsRequest;
use App\Jobs\LegalAspects\Contracts\ListCheck\ListCheckContractExportJob;
use App\Jobs\LegalAspects\Contracts\Contractor\ContractorExportJob;
use App\Jobs\LegalAspects\Contracts\Contractor\NotifyResponsibleContractJob;
use App\Jobs\LegalAspects\Contracts\ListCheck\ListCheckCopyJob;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use App\Traits\Filtertrait;
use App\Facades\Mail\Facades\NotificationMail;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportTemplate;
use App\Jobs\LegalAspects\Contracts\Contractor\ContractImportJob;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;
use PDF;

class ContractLesseeController extends Controller
{
    use UserTrait;
    use ContractTrait;
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_r, {$this->team}", ['except' => ['getInformation', 'multiselect', 'getListCheckItems', 'qualifications', 'saveQualificationItems', 'update', 'listCheckCopy']] );
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
                    'sau_ct_list_check_resumen.total_p_nc AS total_p_nc',
                    'sau_ct_list_check_resumen.list_qualification_id AS id_qualification'
                    )
                    ->leftJoin('sau_ct_list_check_qualifications', function ($join) 
                    {
                      $join->on("sau_ct_list_check_qualifications.contract_id", "sau_ct_information_contract_lessee.id");
                      $join->on('sau_ct_list_check_qualifications.state', DB::raw(1));
                    })
                    ->leftJoin('sau_ct_list_check_resumen', 'sau_ct_list_check_resumen.list_qualification_id', 'sau_ct_list_check_qualifications.id')
                    ->orderBy('sau_ct_information_contract_lessee.social_reason');

        $url = "/legalaspects/contractor";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $contracts->rangePercentageCumple($filters["rangePC"]);
        }

        return Vuetable::of($contracts)
            ->addColumn('legalaspects-contracts-view-list-check', function ($contract) {
                
                if ($contract->type == 'Contratista' || $contract->type == 'Proveedor')
                    return true;

                return false;
            })
            ->addColumn('retrySendMail', function ($contract) {
                $users = $this->getUsersContract($contract->id, $this->company, true);
                if ($users[0]->active == 'SI')
                    return true;

                return false;
            })
            ->addColumn('reactiveUser', function ($contract) {
                $users = $this->getUsersContract($contract->id, $this->company, true);
                if ($users[0]->active == 'NO')
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

            $activitiesContract = [];

            if($request->has('activity_id'))
                $activitiesContract = $this->getDataFromMultiselect($request->activity_id);

            $contract->activities()->sync($activitiesContract);

            $proyectsContract = [];

            if($request->has('proyects_id'))
                $proyectsContract = $this->getDataFromMultiselect($request->proyects_id);

            $contract->proyects()->sync($proyectsContract);

            /*if ($request->has('documents'))
                $this->saveDocuments($request->documents, $contract);*/

            $user = User::where('email', trim(strtolower($request->email)))->first();

            if (!$user)
            {
                $document_user = User::where('document', $request->document)->active()->first();

                if(!$document_user)
                {
                    $user = $this->createUser($request);

                    if ($user == $this->respondHttp500() || $user == null) {
                        return $this->respondHttp500();
                    }
                }
                else
                {
                    return $this->respondWithError('El documento ingresado para el responsable de contratista ya se encuentra activo en el sistema, por lo tanto no puede ser procesado, por favor contacte con el administrador');
                }
            }
            else
            {
                $user->companies()->attach($this->company);

                $company = Company::find($this->company);

                NotificationMail::
                    subject('Creación de contratista en sauce')
                    ->message("Usted acaba de ser creado como contratista en la empresa <b>{$company->name}</b>, por favor ingrese a Sauce y seleccione esta empresa para que pueda ingresar su información.")
                    ->recipients($user)
                    ->module('contracts')
                    ->buttons([['text'=>'Ir a Sauce', 'url'=>url("/")]])
                    ->company($this->company)
                    ->send();

                
                $log_modify = new LogUserModify;
                $log_modify->company_id = $this->company;
                $log_modify->modifier_user = $this->user->id;
                $log_modify->modified_user = $user->id;
                $log_modify->modification = 'Se asigno como usuario responsable de la contratista '.$contract->social_reason;
                $log_modify->save();
            }

            if ($request->type == 'Proveedor')
                $request->type = 'Contratista';
            
            $user->attachRole($this->getIdRole($request->type), $this->team);
            $contract->users()->sync($user);
            

            $responsibles = [];

            if($request->has('users_responsibles'))
                $responsibles = $this->getDataFromMultiselect($request->users_responsibles);

            $contract->responsibles()->sync($responsibles);

            $this->saveLogActivitySystem('Contratistas - Contratistas', 'Se creo el contratista '.$contract->social_reason.' - '.$contract->nit);

            DB::commit();

            if($request->has('users_responsibles'))
                NotifyResponsibleContractJob::dispatch($responsibles, $request->social_reason, $this->company);

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

            $activity_id = [];

            foreach ($contract->activities as $key => $value)
            {                
                array_push($activity_id, $value->multiselect());
            }

            $contract->multiselect_activity = $activity_id;
            $contract->activity_id = $activity_id;

            $proyects_id = [];

            foreach ($contract->proyects as $key => $value)
            {                
                array_push($proyects_id, $value->multiselect());
            }

            $contract->multiselect_proyect = $proyects_id;
            $contract->proyects_id = $proyects_id;

            $users_responsibles = [];

            foreach ($contract->responsibles as $key => $value)
            {                
                array_push($users_responsibles, $value->multiselect());
            }

            $contract->usersContract = $contract->users;

            $contract->multiselect_users_responsibles = $users_responsibles;
            $contract->users_responsibles = $users_responsibles;

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
            $contract->multiselect_contracts = [];
            $contract->delete = [
                'files' => []
            ];

            if ($this->user->hasRole('Contratista', $this->company))
            {
                $contracts = ContractLesseeInformation::withoutGlobalScopes()
                    ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_information_contract_lessee.id')
                    ->where('sau_user_information_contract_lessee.user_id', $this->user->id)
                    ->where('sau_ct_information_contract_lessee.id', '<>', $contract->id)
                    ->get();

                $doc_act_id = collect([]);

                foreach ($contract->activities as $key => $value)
                {                
                    $docs = ActivityDocument::where('activity_id', $value->id)->where('type', 'Contratista')->get();

                    foreach ($docs as $value2)
                    {
                        $doc_act_id->push($value2->id);
                    }
                }

                $documents = ContractDocument::where('company_id', $this->company)->whereNull('document_id')->get();

                $documents2 = ContractDocument::where('company_id', $this->company)->whereIn('document_id', $doc_act_id)->get();

                $documents = $documents->merge($documents2);

                $contract->documents = $this->getFilesByDocuments($contract, $documents);

                $contracts = $contracts->filter(function($contract, $key) {
                    return $this->user->hasRole('Contratista', $contract->company_id);
                })
                ->map(function($contract, $key) {
                    return $contract->multiselectCompany();
                });            

                $contract->multiselect_contracts = $contracts;
                $contract->existsOthersContract = $contracts->count() > 0 ? true : false;
            }

            return $this->respondHttp200([
                'data' => $contract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getFilesByDocuments($contract, $documents)
    {
        if ($documents->count() > 0)
        {
            $contract = $this->getContractUser($this->user->id, $this->company);
            $documents = $documents->transform(function($document, $key) use ($contract) {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
                $document->files = [];

                $files = FileUpload::select(
                    'sau_ct_file_upload_contracts_leesse.id AS id',
                    'sau_ct_file_upload_contracts_leesse.name AS name',
                    'sau_ct_file_upload_contracts_leesse.file AS file',
                    'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                )
                ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                ->join('sau_ct_file_document_contract', 'sau_ct_file_document_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                ->where('sau_ct_file_document_contract.document_id', $document->id)
                ->where('sau_ct_file_document_contract.contract_id', $contract->id)
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

    public function listCheckCopy(Request $request)
    {
        $contract = $this->getContractUser($this->user->id);

        try
        {
            ListCheckCopyJob::dispatch($this->user, $this->company, $contract, $request->contract_selected);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
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
        Validator::make($request->all(), [
            "documents.*.files.*.file" => [
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
            if ($request->active == 'SI' && ($request->active != $contract->active))
            {
                if (!$this->checkLimit())
                    return $this->respondWithError('Límite alcanzado..!! No puede habilitar esta contratista o arrendatario hasta que inhabilite alguno de ellos.');
            }

            $contract->fill($request->all());
            
            if ($request->type == 'Arrendatario')
                $contract->classification = NULL;

            if ($request->has('isInformation'))
            {
                $contract->completed_registration = 'SI';

                $arl = $this->tagsPrepare($request->get('arl'));
                $this->tagsSave($arl, TagsArl::class);

                $social_security_payment_operator = $this->tagsPrepare($request->get('social_security_payment_operator'));
                $this->tagsSave($social_security_payment_operator , TagsSocialSecurityPaymentOperator::class);

                $ips = $this->tagsPrepare($request->get('ips'));
                $this->tagsSave($ips, TagsIps::class);

                $height_training_centers = $this->tagsPrepare($request->get('height_training_centers'));
                $this->tagsSave($height_training_centers, TagsHeightTrainingCenter::class);

                $contract->arl = $arl->implode(',');
                $contract->social_security_payment_operator = $social_security_payment_operator->implode(',');
                $contract->ips = $ips->implode(',');
                $contract->height_training_centers = $height_training_centers->implode(',');

                if ($request->has('documents') && COUNT($request->documents) > 0)
                    $this->saveDocumentsContracts($contract, $request->documents);

                if ($request->has('delete') && COUNT($request->delete) > 0)
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
            }
            else
            {
                $risks = ($request->high_risk_work == 'SI') ? $this->getDataFromMultiselect($request->high_risk_type_id) : [];
                $contract->highRiskType()->sync($risks);

                $activitiesContract = [];

                if($request->has('activity_id'))
                    $activitiesContract = $this->getDataFromMultiselect($request->activity_id);

                $contract->activities()->sync($activitiesContract);

                $proyectsContract = [];

                if($request->has('proyects_id'))
                    $proyectsContract = $this->getDataFromMultiselect($request->proyects_id);

                $contract->proyects()->sync($proyectsContract);

                $responsibles = [];
                
                if($request->has('users_responsibles'))
                    $responsibles = $this->getDataFromMultiselect($request->users_responsibles);

                $contract->responsibles()->sync($responsibles);
            }

            if (!$contract->update())
                return $this->respondHttp500();

            $users = $this->getUsersContract($contract->id);

            foreach ($users as $user)
            {
                if ($contract->type == 'Proveedor')
                    $contract->type = 'Contratista';
                    
                $user->syncRoles([$this->getIdRole($contract->type)], $this->team);
            }

            $qualification_list = ListCheckQualification::where('contract_id', $contract->id)->where('state', true)->first();

            if ($qualification_list)
                $this->reloadLiskCheckResumen($contract, $qualification_list->id);

            
            $this->saveLogActivitySystem('Contratistas - Contratistas', 'Se edito el contratista '.$contract->social_reason.' - '.$contract->nit);

            DB::commit();

            /*if (!$request->has('isInformation') && COUNT($responsibles) > 0)
                NotifyResponsibleContractJob::dispatch($responsibles, $request->social_reason, $this->company);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
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
                $query->orWhere('social_reason', 'like', $keyword);
            });

            if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            {
                $contract = $this->getContractUser($this->user->id, $this->company);
                
                $contracts->where('sau_ct_information_contract_lessee.id', $contract->id);
            }

            $contracts = $contracts->orderBy('social_reason')->take(30)->pluck('id', 'nit');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($contracts)
            ]);
        }
        else
        {
            $contracts = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id as id,
                CONCAT(sau_ct_information_contract_lessee.nit, ' - ',sau_ct_information_contract_lessee.social_reason) as nit
            ")
            ->orderBy('social_reason')
            ->pluck('id', 'nit');
        
            return $this->multiSelectFormat($contracts);
        }
    }

    public function multiselectStandard(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $items = SectionCategoryItems::selectRaw("
                sau_ct_section_category_items.id as id,
                sau_ct_section_category_items.item_name as item_name
            ")
            ->where(function ($query) use ($keyword) {
                $query->orWhere('item_name', 'like', $keyword);
            })
            ->orderBy('item_name')
            ->take(30)->pluck('id', 'item_name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($items)
            ]);
        }
        else
        {
            $items = SectionCategoryItems::selectRaw("
                sau_ct_section_category_items.id as id,
                sau_ct_section_category_items.item_name as item_name
            ")
            ->orderBy('item_name')
            ->pluck('id', 'item_name');
        
            return $this->multiSelectFormat($items);
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
            $limit = 1000;

        $count_contracts = ContractLesseeInformation::where('active', 'SI')->count();

        if ($count_contracts < $limit)
            return true;

        return false;
    }

     public function getListCheckItemsValidations(Request $request)
    {
        $items = SectionCategoryItems::select('*')
            ->get()
            ->transform(function($item, $key){
                $record = $item->itemStandardCompany($this->company)->first();

                if ($record)
                    $item->required = $record->pivot->required;
                else
                    $item->required = 'SI';

                return $item;
            });

        return $this->respondHttp200([
                'items' => $items
            ]);
    }

    public function saveItemsStandard(Request $request)
    {
        $data = [];

        foreach ($request->items as $key => $value)
        {
            $data[] = json_decode($value, true);
        }

        $ids = [];

        foreach ($data as $value)
        {
            $ids[$value["id"]] = ['required'=>$value["required"]];
        }

        $company = Company::find($this->company);

        $company->itemStandardCompany()->sync($ids);

        return $this->respondHttp200([
            'message' => 'Se actualizo la informaciòn'
        ]);
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

            $qualification_list = ListCheckQualification::where('contract_id', $contract->id)->where('state', true)->first();
            
            if (!$qualification_list)
                return $this->respondWithError('La contratista no tiene una calificación activa');
            else
            {
                $items = $this->getStandardItemsContract($contract);

                $qualifications = Qualifications::pluck("name", "id");

                //Obtiene los items calificados
                $items_calificated = ItemQualificationContractDetail::
                        where('contract_id', $contract->id)
                        ->where('list_qualification_id', $qualification_list->id)
                        ->pluck("qualification_id", "item_id");

                $items_aprove = ItemQualificationContractDetail::
                    where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification_list->id)
                    ->pluck("state_aprove_qualification", "item_id");

                $items_reason_reject = ItemQualificationContractDetail::
                    where('contract_id', $contract->id)
                    ->where('list_qualification_id', $qualification_list->id)
                    ->pluck("reason_rejection", "item_id");
                
                $items_observations = ItemQualificationContractDetail::
                        where('contract_id', $contract->id)
                        ->where('list_qualification_id', $qualification_list->id)
                        ->pluck("observations", "item_id");

                if (COUNT($items) > 0)
                {
                    $items->transform(function($item, $index) use ($qualifications, $items_calificated, $contract, $items_observations, $qualification_list, $items_aprove, $items_reason_reject) {
                        //Añade las actividades definidas de cada item para los planes de acción
                        $item->activities_defined = $item->activities()->pluck("description");
                        $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                        $item->observations = (isset($items_observations[$item->id]) && $items_observations[$item->id] != 'null') ? $items_observations[$item->id] : '';
                        $item->list_qualification_id = $qualification_list->id;
                        $item->state_aprove_qualification = isset($items_aprove[$item->id]) ? $items_aprove[$item->id] : 'NULL';
                        
                        if (isset($items_reason_reject[$item->id]))
                            $item->reason_rejection = isset($items_reason_reject[$item->id]) ? $items_reason_reject[$item->id] : 'NULL';

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
                                    ->where('sau_ct_file_item_contract.list_qualification_id', $qualification_list->id)
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
                                    ->where('list_qualification_id', $qualification_list->id)
                                    ->where('item_id', $item->id)
                                    ->first();

                            $item->actionPlan = ActionPlan::model($model_activity)->prepareDataComponent();
                        }

                        return $item;
                    });

                    $data = [
                        'id' => $contract->id,
                        'id_qualification_list' => $qualification_list->id,
                        'items' => $items,
                        'delete' => [
                            'files' => []
                        ]
                    ];
                }
                else
                    $data = [];

            }

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

    public function saveDocuments(DocumentRequest $request)
    {
        DB::beginTransaction();

        try
        {
            if ($request->has('documents'))
            {
                foreach ($request->documents as $value)
                {
                    $id = isset($value['id']) ? $value['id'] : NULL;
                    ContractDocument::updateOrCreate(['id'=>$id], ['company_id'=>$this->company, 'name'=>$value['name']]);
                }
            }

            if ($request->has('delete'))
                $this->deleteData($request->delete);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se guardaron los documentos'
        ]);
    }

    public function getDocuments()
    {
        $documents = ContractDocument::whereNull('document_id')->get();

        foreach ($documents as $document)
        {
            $document->key = Carbon::now()->timestamp + rand(1,10000);
        }

        return $this->respondHttp200([
            'delete' => [],
            'documents' => $documents
        ]);
    }

    private function deleteData($data)
    {    
        if (COUNT($data) > 0)
            ContractDocument::destroy($data);
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
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        

                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt')
                        {
                            $fail('Archivo debe ser un pdf, doc, docx, xlsx, xls, ppt, pptx');
                        }
                    }
                    /*if (!is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');*/
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            if ($request->has('list_qualification_id') && $request->list_qualification_id)
                $qualification_list = ListCheckQualification::findOrFail($request->list_qualification_id);
                
            $data = $request->except(['items', 'files_binary']);

            $qualifications = Qualifications::pluck("id", "name");

            if ($request->has('contract_id') && $request->contract_id)                
                $contract = ContractLesseeInformation::findOrFail($request->contract_id);
            else
            {
                if($qualification_list)
                    $contract = ContractLesseeInformation::findOrFail($qualification_list->contract_id);
                else
                    $contract = $this->getContractUser($this->user->id);
            }

            //Se inician los atributos necesarios que seran estaticos para todas las actividades
            // De esta forma se evitar la asignacion innecesaria una y otra vez 
            ActionPlan::
                    user($this->user)
                ->module('contracts')
                ->url(url('/administrative/actionplans'));

            if ($request->has('qualification') && $request->qualification)
            {
                try
                {
                    $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
                    
                } catch (\Exception $e) {
                    $exist = 'NO';
                }

                $itemQualification = ItemQualificationContractDetail::updateOrCreate(
                    [
                        'contract_id' => $contract->id, 
                        'item_id' => $request->id, 
                        'list_qualification_id' => $request->list_qualification_id
                    ],
                    [
                        'contract_id' => $contract->id, 
                        'item_id' => $request->id,                    
                        'qualification_id' => $qualifications[$request->qualification], 
                        'observations' => $request->observations, 
                        'list_qualification_id' => $request->list_qualification_id
                    ]);

                if ($exist == 'SI')
                    $itemQualification->update(['state_aprove_qualification' => 'PENDIENTE']);
                

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

                                $beforeFile = $fileUpload;

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
                                $data['files'][$keyF]['file'] = $nameFile;
                                $data['files'][$keyF]['old_name'] = $nameFile;
                            }

                            $fileUpload->name = $file['name'];
                            $fileUpload->expirationDate = $file['expirationDate'] == null ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd');

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
                                $state->contract_id = $contract->id;
                                $state->file_id = $fileUpload->id;
                                $state->module = 'Lista de chequeo';
                                $state->state = 'CREADO';                                
                                $state->date = date('Y-m-d');
                                $state->save();
                            }
                                
                            $data['files'][$keyF]['id'] = $fileUpload->id;

                            $fileUpload->contracts()->sync([$contract->id]);
                            
                            $ids[$request->id] = ['list_qualification_id' => $request->list_qualification_id];

                            $fileUpload->items()->sync($ids);
                            //$fileUpload->items()->sync([$request->id]);
                        }

                        //Borrar archivos reemplazados
                        foreach ($files_names_delete as $keyf => $file)
                        {
                            Storage::disk('s3')->delete('legalAspects/files/'. $file);
                        }
                    }
                }

                /**Planes de acción*/

                $detail_procedence = 'Contratista - Estándares mínimos. Estándar: ' . $request->item_name;

                ActionPlan::
                        model($itemQualification)
                    ->detailProcedence($detail_procedence)
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
                        Storage::disk('s3')->delete('legalAspects/files/'. $file['old_name']);
                    }
                }
            }

            ActionPlan::sendMail();

            DB::commit();

            $data['observations'] = $data['observations'] != 'null' ? $data['observations'] : '';

            //$this->sendNotification($contract);

            return $this->respondHttp200([
                'data' => $data
            ]);  

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
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

    public function reactiveUser(ContractLesseeInformation $contract)
    {
        $users = $this->getUsersContract($contract->id, $this->company, true);

        try
        {  
            if ($users->count() > 0)
            {
                if ($users[0]->active == 'NO')
                {
                    $users[0]->update(['active' => 'SI']);
                    return $this->retrySendMail($contract);
                }
            }
        } catch(Exception $e) {
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
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
                ->orderBy('name')
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

    public function multiselectUsers(Request $request)
    {
        $users = $this->getUsersMasterContract($this->company);
        $users = $users->pluck('id', 'name');

        return $this->multiSelectFormat($users);

    }

    public function saveDocumentsContracts($contract, $documents)
    {
        foreach ($documents as $document)
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
                        $state->contract_id = $contract->id;
                        $state->file_id = $fileUpload->id;
                        $state->module = 'Documentos globales';
                        $state->state = 'CREADO';                        
                        $state->date = date('Y-m-d');
                        $state->save();
                    }

                    $fileUpload->contracts()->sync([$contract->id]);
                    $ids = [];
                    $ids[$document['id']] = ['contract_id' => $contract->id];
                    $fileUpload->documentsContract()->sync($ids);
                }

                //Borrar archivos reemplazados
                foreach ($files_names_delete as $keyf => $file)
                {
                    Storage::disk('s3')->delete('legalAspects/files/'. $file);
                }
            }
        }
    }

    public function downloadTemplateImport()
    {
        return Excel::download(new ContractsImportTemplate($this->company), 'PlantillaImportacionContratistas.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        ContractImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function destroy(ContractLesseeInformation $contract)
    {
        DB::beginTransaction();

        try
        {
            //Borrado de documentos

            $doc_act_id = collect([]);

            foreach ($contract->activities as $key => $value)
            {                
                $docs = ActivityDocument::where('activity_id', $value->id)->where('type', 'Contratista')->get();

                foreach ($docs as $value2)
                {
                    $doc_act_id->push($value2->id);
                }
            }

            $documents = ContractDocument::where('company_id', $this->company)->whereNull('document_id')->get();

            $documents2 = ContractDocument::where('company_id', $this->company)->whereIn('document_id', $doc_act_id)->get();

            $documents = $documents->merge($documents2);

            if ($documents->count() > 0)
            {
                foreach ($documents as $document)
                {
                    $files = FileUpload::select(
                        'sau_ct_file_upload_contracts_leesse.id AS id'
                    )
                    ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                    ->join('sau_ct_file_document_contract', 'sau_ct_file_document_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                    ->where('sau_ct_file_upload_contract.contract_id', $contract->id)
                    ->where('sau_ct_file_document_contract.document_id', $document->id)
                    ->where('sau_ct_file_document_contract.contract_id', $contract->id)
                    ->get();
                
                    foreach ($files as $file)
                    {
                        $file_delete = FileUpload::find($file);
    
                        if ($file_delete)
                        {
                            foreach ($file_delete as $file_2) 
                            {
                                $path = $file_2->file;
                                $file_2->delete();
                                Storage::disk('s3')->delete('legalAspects/files/'. $path);
                            }
                        }
                    }
                }
            }

            //Borrado evaluaciones

            $evaluationContract = EvaluationContract::where('contract_id', $contract->id)->get();

            foreach ($evaluationContract as $evaluation)
            {
                foreach ($evaluation->items as $item)
                {
                    ActionPlan::model($item)->modelDeleteAll();
                }

                foreach ($evaluation->files  as $file)
                {
                    $file_delete = EvaluationFile::find($file);

                    if ($file_delete)
                    {
                        foreach ($file_delete as $file_2) 
                        {
                            Storage::disk('s3')->delete($file_2->path_client(false)."/".$file_2->file);
                            $file_2->delete();
                        }
                    }
                }

                $evaluation->delete();
            }

            //Borrado de empleados

            foreach ($contract->employees as $employee)
            {
                $files = DB::table('sau_ct_file_document_employee')->where('employee_id', $employee->id)->get();

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

                $employee->delete();
            }

            // Borrado de actividades

             $contract->activities()->sync([]);

            // Borrado de calificaciones de lista de chequeo

            $qualification_list = ListCheckQualification::where('contract_id', $contract->id)->get();

            foreach ($qualification_list as $qualification)
            {
                foreach ($qualification->files as $file) 
                {                    
                    $file->delete();
                    Storage::disk('s3')->delete('legalAspects/files/'. $file->file);
                }

                $qualification->delete();
            }

            //Borrado de usuarios o desactivacion.

            $users = $contract->users;

            foreach ($users as $key => $value) 
            {
                $count_contract = User::select(
                    'sau_users.*')
                ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                ->where('sau_user_information_contract_lessee.user_id', $value->id)
                ->get();

                if ($count_contract->count() > 1)
                    $contract->users()->detach($value->id);
                else
                {
                    $user_desactive = User::find($value->id);
                    $user_desactive->active = 'NO';
                    $user_desactive = $user_desactive->save();
                }
            }

            //Borrado de contratista

            $this->saveLogDelete('Contratistas', 'Se elimino la contratista '.$contract->social_reason.'-'.$contract->nit);

            if(!$contract->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la contratista'
        ]);
    }

    public function getInformationActivities(Request $request)
    {
        $activities = ActivityContract::get();
        $contracts_totals = ContractLesseeInformation::get();
        $contracts = [];

        foreach ($contracts_totals as $key => $value) 
        {
            if ($value->activities()->count() == 0)  
                array_push($contracts, ["id" => $value->id, "name" => $value->social_reason, "selection" => []]);
        }

        return $this->respondHttp200([
            'data' => [
                'contracts' => $contracts,
                'activities' => $activities
            ]
        ]);
    }

    public function saveMasiveActivities(Request $request)
    {
        if ($request->has('contracts') && $request->get('contracts'))
        {
            foreach ($request->get('contracts') as $key => $value)
            {
                $data['contracts'][$key] = json_decode($value, true);
                $request->merge($data);
            }
        }

        foreach ($request->contracts as $key => $contract) 
        {   
            $contract_sync = ContractLesseeInformation::find($contract['id']);

            if ($contract['selection'])
                $contract_sync->activities()->sync($contract['selection']);
        }
    }

    public function getInformationResponsibles(Request $request)
    {
        $responsibles = $this->getUsersMasterContract($this->company);
        $contracts_totals = ContractLesseeInformation::get();
        $contracts = [];

        foreach ($contracts_totals as $key => $value) 
        {
            if ($value->responsibles()->count() == 0)  
                array_push($contracts, ["id" => $value->id, "name" => $value->social_reason, "selection" => []]);
        }

        return $this->respondHttp200([
            'data' => [
                'contracts' => $contracts,
                'responsibles' => $responsibles
            ]
        ]);
    }

    public function saveMasiveResponsibles(Request $request)
    {
        if ($request->has('contracts') && $request->get('contracts'))
        {
            foreach ($request->get('contracts') as $key => $value)
            {
                $data['contracts'][$key] = json_decode($value, true);
                $request->merge($data);
            }
        }

        foreach ($request->contracts as $key => $contract) 
        {   
            $contract_sync = ContractLesseeInformation::find($contract['id']);

            if ($contract['selection'])
                $contract_sync->responsibles()->sync($contract['selection']);
        }
    }

    public function verifyValidateQualificationListCheck()
    {
        try
        {
            $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
            
        } catch (\Exception $e) {
            $exist = 'NO';
            }
        if ($exist == 'SI')
            return $this->respondHttp200([
                'data' => true
            ]);
        else
            return $this->respondHttp200([
                'data' => false
            ]);
    }

    public function aproveQualification(Request $request)
    {
        $items_calificated = ItemQualificationContractDetail::
            where('contract_id', $request->contract_id)
            ->where('list_qualification_id', $request->list_id)
            ->where('item_id', $request->item_id)
            ->first();

        $items_calificated->update(['state_aprove_qualification' => 'APROBADA','reason_rejection' => NULL]);

        return $this->respondHttp200([
            'message' => 'Se actualizo la calificación'
        ]);
    }

    public function desaproveQualification(Request $request)
    {
        if ($request->reason_rejection)
        {
            $items_calificated = ItemQualificationContractDetail::
            where('contract_id', $request->contract_id)
            ->where('list_qualification_id', $request->list_id)
            ->where('item_id', $request->item_id)
            ->first();

            $items_calificated->update(['state_aprove_qualification' => 'RECHAZADA', 'reason_rejection' => $request->reason_rejection, 'qualification_id' => 2]);

            return $this->respondHttp200([
                'message' => 'Se actualizo la calificación'
            ]);
        }
        else
            return $this->respondWithError('Debe adicionar un motivo de rechazo');
    }

    public function downloadPdf($id)
    {
        $listCheck = $this->getDataExportPdf($id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.qualificationListCheck', ['listCheck' => $listCheck] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('lista de estandares mínimos.pdf');
    }

    public function getDataExportPdf($id)
    {
        $listCheck = $this->getListCheckItemsPdf($id);

        return $listCheck;
    }

    public function getListCheckItemsPdf($id)
    {
        $contract = ContractLesseeInformation::findOrFail($id);

        $qualification_list = ListCheckQualification::where('contract_id', $contract->id)->where('state', true)->first();

        $items = $this->getStandardItemsContract($contract);

        $qualifications = Qualifications::pluck("name", "id");

        //Obtiene los items calificados
        $items_calificated = ItemQualificationContractDetail::
                    where('contract_id', $contract->id)
                ->where('list_qualification_id', $qualification_list->id)
                ->pluck("qualification_id", "item_id");
        
        $items_observations = ItemQualificationContractDetail::
                    where('contract_id', $contract->id)
                ->where('list_qualification_id', $qualification_list->id)                     
                ->pluck("observations", "item_id");

        $items_aprove = ItemQualificationContractDetail::
            where('contract_id', $contract->id)
            ->where('list_qualification_id', $qualification_list->id)
            ->pluck("state_aprove_qualification", "item_id");

        $items_reason_reject = ItemQualificationContractDetail::
            where('contract_id', $contract->id)
            ->where('list_qualification_id', $qualification_list->id)
            ->pluck("reason_rejection", "item_id");

        $compliance = [
            'cumple' => 0,
            'no_cumple' => 0,
            'no_aplica' => 0,
            'total' => 0       
        ];

        if (COUNT($items) > 0)
        {
            $items->transform(function($item, $index) use ($qualifications, $items_calificated, $contract, $items_observations, $qualification_list, $items_aprove, $items_reason_reject,&$compliance) {
                //Añade las actividades definidas de cada item para los planes de acción
                $item->activities_defined = $item->activities()->pluck("description");
                $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';
                $item->observations = (isset($items_observations[$item->id]) && $items_observations[$item->id] != 'null') ? $items_observations[$item->id] : '';
                //$item->observations = isset($items_observations[$item->id]) ? $items_observations[$item->id] : '';
                $item->list_qualification_id = $qualification_list->id;
                $item->state_aprove_qualification = isset($items_aprove[$item->id]) ? $items_aprove[$item->id] : 'NULL';
                        
                if (isset($items_reason_reject[$item->id]))
                    $item->reason_rejection = isset($items_reason_reject[$item->id]) ? $items_reason_reject[$item->id] : 'NULL';

                $item->files = [];
                $item->actionPlan = [
                    "activities" => [],
                    "activitiesRemoved" => []
                ];

                if ($item->qualification == 'NA')
                    $compliance['no_aplica']++;

                if ($item->qualification == 'C')
                {
                    $compliance['cumple']++;
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
                            ->where('sau_ct_file_item_contract.list_qualification_id', $qualification_list->id)
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
                    $compliance['no_cumple']++;
                    $model_activity = ItemQualificationContractDetail::
                                where('contract_id', $contract->id)
                            ->where('item_id', $item->id)
                            ->where('list_qualification_id', $qualification_list->id)
                            ->first();

                    $item->actionPlan = ActionPlan::model($model_activity)->prepareDataComponent();
                }

                $compliance['total']++;

                return $item;
            });

            $compliance['p_cumple'] = round(($compliance['cumple']/$compliance['total'])*100, 2);
            $compliance['p_no_aplica'] = round(($compliance['no_aplica']/$compliance['total'])*100, 2);
            $compliance['p_total'] = round((($compliance['cumple']+$compliance['no_aplica'])/$compliance['total'])*100, 2);


            if ($compliance['no_cumple'] > 0)
            {
                $compliance['p_no_cumple']  = $compliance['total']-$compliance['cumple']-$compliance['no_aplica']+$compliance['no_cumple'];
                $compliance['pp_no_cumple'] = round(($compliance['p_no_cumple']/$compliance['total'])*100, 2);
            }
            else
            {
                $compliance['p_no_cumple']  = $compliance['total']-$compliance['cumple']-$compliance['no_aplica'];
                $compliance['pp_no_cumple'] = round(($compliance['p_no_cumple']/$compliance['total'])*100, 2);
            }

            $qualifications_creator = ListCheckQualification::select(
                'sau_ct_list_check_qualifications.*',
                DB::raw("case when sau_ct_list_check_qualifications.state is true then 'ACTIVA' else 'INACTIVA' end as state_list"),
                'sau_users.name as user_creator')
            ->join('sau_users', 'sau_users.id', 'sau_ct_list_check_qualifications.user_id')
            ->where('company_id', $this->company)
            ->where('contract_id', $contract->id)
            ->where('sau_ct_list_check_qualifications.id', $qualification_list->id)
            ->first();

            $data = [
                'id' => $contract->id,
                'items' => $items,
                'id_qualification_list' => $qualification_list->id,
                'delete' => [
                    'files' => []
                ],
                'validity_period' => $qualification_list->validity_period,
                'user_Creator' => $qualifications_creator->user_creator,
                'state' => $qualifications_creator->state_list,
                'contract_name' => $contract->social_reason,
                'cumplimiento' => $compliance
            ];
        }
        else
            $data = [];

        return  $data;
    }

    public function multiselectSocialSecurity(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsSocialSecurityPaymentOperator::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsSocialSecurityPaymentOperator::selectRaw("
                sau_ct_tag_social_security_payment_operator.id as id,
                sau_ct_tag_social_security_payment_operator.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectIps(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsIps::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsIps::selectRaw("
                sau_ct_tag_ips.id as id,
                sau_ct_tag_ips.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectHeightTrainingCenter(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsHeightTrainingCenter::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsHeightTrainingCenter::selectRaw("
                sau_ct_tag_height_training_centers.id as id,
                sau_ct_tag_height_training_centers.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectArl(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsArl::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsArl::selectRaw("
                sau_ct_tag_arl.id as id,
                sau_ct_tag_arl.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }
}
