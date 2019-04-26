<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LegalAspects\Contracts\ContractRequest;
use App\Http\Requests\LegalAspects\Contracts\ContractCompleteInfoRequest;
use App\Http\Requests\LegalAspects\Contracts\ListCheckItemsContractRequest;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\Administrative\ActionPlans\ActionPlansActivityModule;
use App\Models\General\Module;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Traits\UserTrait;
use Session;
use DB;
use Validator;
use Carbon\Carbon;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Facades\Mail\Facades\NotificationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ContractLesseeController1 extends Controller
{
    use UserTrait;
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
        $user = User::find(Auth::user()->id);
        if (count($user->contractInfo) > 0) {
            $sql = SectionCategoryItems::select(
                'sau_ct_section_category_items.*',
                'sau_ct_standard_classification.standard_name as name'
            )
            ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
            ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');
            if ($user->contractInfo[0]->classification == "upa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '3 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '7 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 10 && $user->contractInfo[0]->number_workers <= 50 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '21 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 10 && $user->contractInfo[0]->number_workers <= 50 && $user->contractInfo[0]->risk_class == "Clase de riesgo IV y V") {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "upa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo IV y V") {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 50) {
                $items = $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
            //Añade las actividades definidas de cada item para los planes de acción
            $items->transform(function($item, $index){
                $item['activities_defined'] = $item->activities()->pluck("description");
                return $item;
            });

            //Proceso de verificar si tiene calificaciones
            $validate_items_calificated = ItemQualificationContractDetail::where('user_id', '=', $user->id)->get();
            if ($validate_items_calificated) {
                foreach ($validate_items_calificated as $value) {
                    foreach ($items as $item) {
                        if ($item['id'] == $value['item_id']) {
                            $item['qualification'] = (string)$value['qualification_id'];
                        }
                    }
                }
            }

            return $items;
        } else {
            return $this->respondHttp500([
                'message' => 'El usuario auntenticado no tiene una información de contratistas'
            ]);
        }
    }

    
    public function store(ContractRequest $request)
    {   
        $user = $this->createUser($request);
        $contractLesseeInformation = new ContractLesseeInformation;

        if ($user == $this->respondHttp500() || $user == null) {
             return $this->respondHttp500();
        }

        $user->companies()->sync(Session::get('company_id'));
        $role_id = Role::where('name', '=', $request->role)->withoutGlobalScopes()->pluck("id");
        $user->syncRoles([$role_id[0]]);
        $contractLesseeInformation->company_id = Session::get('company_id');
        $contractLesseeInformation->nit = $request->nit;
        $contractLesseeInformation->type = $request->role;
        $contractLesseeInformation->classification = $request->classification;
        $contractLesseeInformation->business_name = $request->name_business;
        $contractLesseeInformation->social_reason = $request->social_reason;
        if ($request->high_risk) { $contractLesseeInformation->high_risk_work = 1; }
        if (!$contractLesseeInformation->save()) {  return $this->respondHttp500(); }

        $user->contractInformation()->sync($contractLesseeInformation);

        return $this->respondHttp200([
            'message' => 'Se creo el usuario'
        ]);
        
    }

    /**
     * Update the given user.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(ContractCompleteInfoRequest $request, $id)
    {
        $user = User::find($id);
        $contractInfoComplete = $user->contractInfo[0]->fill($request->all());

        if(!$contractInfoComplete->save()){
            return $this->respondHttp500();
        }
  
        return $this->respondHttp200([
            'message' => 'Se actualizo la información'
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function qualifications(Request $request)
    {
        $qualifications = new Qualifications;
        return $qualifications->pluck("description","id");
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function saveQualificationItems(Request $request)
    {
        DB::beginTransaction();
        
        // Función de validaciones de archivos y planes de acción
        $this->rulesListContract($request);
        $user = User::find(Auth::user()->id);
        $itemsCalificated = [];
        $countQualification = 0;

        foreach ($request->items as $item)
        {
            if(isset($item['qualification'])) {
                $countQualification++;
                array_push($itemsCalificated, $item);
            }
        }

        if ($countQualification == 0) {
            return $this->respondHttp500([
                'message' => 'Debes calificar por lo menos un item para poder guardar la lista de estándares'
            ]);
        }

        $user_id = $user->id;
        $contract_id = $user->contractInfo[0]->id;
        $validate_items = ItemQualificationContractDetail::where('user_id', '=', $user_id)->get();

        //Proceso de delete e insert de la calificacion del detalle
        try {
            if ($validate_items) {
                ItemQualificationContractDetail::where('user_id', '=', $user_id)->delete();
                foreach ($itemsCalificated as $item) {
                    $itemQualificationContractDetail = new ItemQualificationContractDetail;
                    $itemQualificationContractDetail->item_id = $item['id'];
                    $itemQualificationContractDetail->qualification_id = $item['qualification'];
                    $itemQualificationContractDetail->contract_id = $contract_id;
                    $itemQualificationContractDetail->user_id = $user_id;
                    if (!$itemQualificationContractDetail->save()) {  return $this->respondHttp500(); }
                }
            }else{
                foreach ($itemsCalificated as $item) {
                    $itemQualificationContractDetail = new ItemQualificationContractDetail;
                    $itemQualificationContractDetail->item_id = $item['id'];
                    $itemQualificationContractDetail->qualification_id = $item['qualification'];
                    $itemQualificationContractDetail->contract_id = $contract_id;
                    $itemQualificationContractDetail->user_id = $user_id;
                    if (!$itemQualificationContractDetail->save()) {  return $this->respondHttp500(); }
                    // ItemQualificationContractDetail::sync($item['id'], $item['qualification'], $contract_id, $user_id);
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        $validate_files = FileUpload::select('sau_ct_file_upload_contracts_leesse.*')
        ->join('sau_ct_file_item_contract', 'sau_ct_file_upload_contracts_leesse.id', '=', 'sau_ct_file_item_contract.file_id')
        ->join('sau_ct_section_category_items', 'sau_ct_file_item_contract.item_id', '=', 'sau_ct_section_category_items.id')
        ->where('user_id', '=', $user_id);

        //Proceso de delete e insert de archivos
        try {
            if ($validate_files->get()) {
                foreach ($validate_files->get() as $file_removed) {
                    Storage::disk('s3')->delete('legalAspects/files/'. $file_removed['file']); //Se borra el archivo del s3
                }
                $validate_files->delete();
                foreach ($itemsCalificated as $item) {
                    if(isset($item['files'])) {
                        if (COUNT($item['files']) > 0) {
                            $filesIdSync = [];
                            foreach ($item['files'] as $i => $file) {
                                $fileUpload = new FileUpload;
                                $nameFile = base64_encode($user_id.$item['id'].now().$i).'.'.$file['file']->extension(); //Se crea el nombre del archivo
                                $file['file']->storeAs('legalAspects/files/', $nameFile,'s3'); // Se guarda el archivo con el nombre especifico
                                $fileUpload->contract_id = $contract_id;
                                $fileUpload->name = $file['name'];
                                $fileUpload->file = $nameFile;
                                $fileUpload->expirationDate = $file['expirationDate'] == "" ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd');
                                $fileUpload->user_id = $user_id;
                                if (!$fileUpload->save()) { return $this->respondHttp500(); }
                                array_push($filesIdSync, $fileUpload->id);
                            }
                            $SectionCategoryItems = SectionCategoryItems::find($item['id']);
                            $SectionCategoryItems->fileSyncInfo()->sync($filesIdSync);
                        }
                    }
                }
            } else {
                foreach ($itemsCalificated as $item) {
                    if(isset($item['files'])) {
                        if (COUNT($item['files']) > 0) {
                            $filesIdSync = [];
                            foreach ($item['files'] as $i => $file) {
                                $fileUpload = new FileUpload;
                                $nameFile = base64_encode($user_id.$item['id'].now().$i).'.'.$file['file']->extension(); //Se crea el nombre del archivo
                                $file['file']->storeAs('legalAspects/files/', $nameFile,'s3'); // Se guarda el archivo con el nombre especifico
                                $fileUpload->contract_id = $contract_id;
                                $fileUpload->name = $file['name'];
                                $fileUpload->file = $nameFile;
                                $fileUpload->expirationDate = $file['expirationDate'] == "" ? null : (Carbon::createFromFormat('D M d Y', $file['expirationDate']))->format('Ymd');
                                $fileUpload->user_id = $user_id;
                                if (!$fileUpload->save()) { return $this->respondHttp500(); }
                                array_push($filesIdSync, $fileUpload->id);
                            }
                            $SectionCategoryItems = SectionCategoryItems::find($item['id']);
                            $SectionCategoryItems->fileSyncInfo()->sync($filesIdSync);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        //Proceso de planes de acción
        try {
            foreach ($itemsCalificated as $item) {
                if (isset($item['actionPlan'])) {
                    if (COUNT($item['actionPlan']['activities']) > 0) {
                        $activities_no_defined = ActionPlansActivity::select('sau_action_plans_activity_module.item_id','sau_action_plans_activities.*')
                        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
                        ->where('sau_action_plans_activity_module.item_id', '=', $item['id'])
                        ->where('sau_action_plans_activities.editable', '=', '')->get();
                        if (COUNT($activities_no_defined) > 0) {
                            foreach ($activities_no_defined as $activity_no_defined ) {
                                ActionPlansActivity::find($activity_no_defined['id'])->delete();
                            }
                        }
                        foreach ($item['actionPlan']['activities'] as $action) {
                            $actionPlansActivity = new ActionPlansActivity;
                            $actionPlansActivity->description = $action['description'];
                            $actionPlansActivity->responsible_id = $action['responsible_id'] == "" ? "1" : $action['responsible_id'];
                            $actionPlansActivity->user_id = $user_id;
                            $actionPlansActivity->execution_date = (Carbon::createFromFormat('D M d Y', $action['execution_date']))->format('Ymd');
                            $actionPlansActivity->expiration_date = (Carbon::createFromFormat('D M d Y', $action['expiration_date']))->format('Ymd');
                            $actionPlansActivity->state = $action['state'];
                            $actionPlansActivity->editable = $action['editable'];
                            $actionPlansActivity->company_id = Session::get('company_id');
                            if (!$actionPlansActivity->save()) { return $this->respondHttp500(); }
                            $actionPlansActivityModule = new ActionPlansActivityModule;
                            $module = Module::where('name', '=', 'contracts')->first();
                            $actionPlansActivityModule->module_id = $module->id;
                            $actionPlansActivityModule->activity_id = $actionPlansActivity->id;
                            $actionPlansActivityModule->item_id = $item['id'];
                            $actionPlansActivityModule->item_table_name = "sau_ct_item_qualification_contract";
                            if (!$actionPlansActivityModule->save()) { return $this->respondHttp500(); }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        DB::commit();

        $users = User::select('sau_users.*')
        ->join('sau_company_user', 'sau_users.id', 'sau_company_user.user_id')
        ->where('sau_company_user.company_id', '=', Session::get('company_id'))->get();

        foreach ($users as $user) {
            if(!($user->contractInfo)){
                \Log::info($user);
            }
        }

        // NotificationMail::
        //     subject('Notificación de usuario en sauce')
        //     ->message('Te damos la bienvenida a la plataforma, se ha generado un nuevo usuario para este correo, para establecer tu nueva contraseña, por favor dar click al siguiente enlace.')
        //     ->recipients($user)
        //     ->buttons([['text'=>'Establecer contraseña', 'url'=>url("/password/generate/".base64_encode($generatePasswordUser->token))]])
        //     ->module('users')
        //     ->subcopy('Este link sólo se puede utilizar una vez')
        //     ->send();

        return $this->respondHttp200([
            'message' => 'La lista de estándares ha sido guardada exitosamente.'
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
            $contract = ContractLesseeInformation::findOrFail($id);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function multiselect(Request $request)
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

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validateFilesItem(Request $request){
        $files = [];
        $validate_files = FileUpload::select('sau_ct_file_upload_contracts_leesse.*', 'sau_ct_section_category_items.id AS item_id')
        ->join('sau_ct_file_item_contract', 'sau_ct_file_upload_contracts_leesse.id', '=', 'sau_ct_file_item_contract.file_id')
        ->join('sau_ct_section_category_items', 'sau_ct_file_item_contract.item_id', '=', 'sau_ct_section_category_items.id')
        ->where('user_id', '=', Auth::user()->id);
        if ($validate_files->get()) {
            foreach ($validate_files->get() as $file) {
                // \Log::info($request->item_id);
                if ($file['item_id'] == $request->item_id){
                    // \Log::info(Storage::disk('s3')->find($file['file']));
                    $data = [
                        'name' => $file['name'],
                        'expirationDate' => (Carbon::createFromFormat('Y-m-d', $file['expirationDate']))->format('D M d Y'),
                        'file' => $file['file'],
                        'file_id' => $file['id']
                    ];
                    array_push($files, $data);
                }
            }
        }
        return $files;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validateActionPlanItem(Request $request){
        //Proceso de verificar si tiene planes de acción
        $activities = [];

        foreach ($request->definedActivities as $activitie) {
            $validate_items_action_plan = ItemQualificationContractDetail::select(
                'sau_ct_item_qualification_contract.item_id',
                'sau_action_plans_activities.*'
            )
            ->leftJoin('sau_action_plans_activity_module', function ($join) {
                $join->on('sau_ct_item_qualification_contract.item_id', '=', 'sau_action_plans_activity_module.item_id');
                $join->on('sau_action_plans_activity_module.item_table_name', '=', \DB::raw("'sau_ct_item_qualification_contract'"));
            })
            ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
            ->where('sau_action_plans_activities.user_id', '=', Auth::user()->id);
            $action_plan = $validate_items_action_plan->orderBy('sau_action_plans_activities.created_at', 'DESC')->where('sau_action_plans_activities.description', '=', $activitie)->first();

            if ($action_plan) {
                //planes de acción listarlos en los items
                $data = [
                    'description' => $activitie,
                    'responsible_id'  => $action_plan['responsible_id'],
                    'execution_date'  => (Carbon::createFromFormat('Y-m-d', $action_plan['execution_date']))->format('D M d Y'),
                    'expiration_date' => (Carbon::createFromFormat('Y-m-d', $action_plan['expiration_date']))->format('D M d Y'),
                    'state' => $action_plan['state'],
                    'editable' => $action_plan['editable']
                ];
                array_push($activities, $data);
            } else {
                $data = [
                    'description' => $activitie,
                    'responsible_id'  => '',
                    'execution_date'  => '',
                    'expiration_date' => '',
                    'state' => '',
                    'editable' => 'NO'
                ];
                array_push($activities, $data);
            }
        }

        $validate_items_action_plan = ItemQualificationContractDetail::select(
            'sau_ct_item_qualification_contract.item_id',
            'sau_action_plans_activities.*'
        )
        ->leftJoin('sau_action_plans_activity_module', function ($join) {
            $join->on('sau_ct_item_qualification_contract.item_id', '=', 'sau_action_plans_activity_module.item_id');
            $join->on('sau_action_plans_activity_module.item_table_name', '=', \DB::raw("'sau_ct_item_qualification_contract'"));
        })
        ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
        ->where('sau_action_plans_activities.user_id', '=', Auth::user()->id)
        ->where('sau_ct_item_qualification_contract.item_id', '=', $request->item_id)
        ->where('sau_action_plans_activities.editable', '=', '');
        if ($validate_items_action_plan->get()) {
            foreach ($validate_items_action_plan->get() as $action) {
                $data = [
                    'description' => $action['description'],
                    'responsible_id'  => $action['responsible_id'],
                    'execution_date'  => (Carbon::createFromFormat('Y-m-d', $action['execution_date']))->format('D M d Y'),
                    'expiration_date' => (Carbon::createFromFormat('Y-m-d', $action['expiration_date']))->format('D M d Y'),
                    'state' => $action['state'],
                    'editable' => $action['editable']
                ];
                array_push($activities, $data);
            }
        }
    
        return $activities;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rulesListContract($request)
    {
        // Como el request->items viene como string se pierden los archivos, entonces..
        // Llegan los archivos por aparte y en el item se especifica cual "file" es de que "item"
        foreach ($request->items as $key => $value)
        {
            $data['items'][$key] = json_decode($value, true);
            if (isset($data['items'][$key]['filesIndex'])) {
                $filesIndex = $data['items'][$key]['filesIndex'];
                if (isset($request->$filesIndex)) {
                    foreach ($request->$filesIndex as $index => $file) {
                        $data['items'][$key]['files'][$index]['file'] = $file;
                    }
                }
            }
            $request->merge($data);
        }

        $messages = [];

        $rules = [
            'items.*.actionPlan.activities.*.execution_date' => 'required',
            'items.*.actionPlan.activities.*.expiration_date' => 'required',
            // 'items.*.actionPlan.activities.*.responsible_id' => 'required', Pendiente por qué en local no se listan los responsables
            'items.*.actionPlan.activities.*.state' => 'required',
            'items.*.files.*.name' => 'required',
            "items.*.files.*.expirationDate" => "nullable|date|after_or_equal:today",
            'items.*.files.*.file' => 'required|file|mimes:pdf'
        ];

        $rulesActionPlan = ActionPlan::prefixIndex('items.*.actionPlan.activities.*.')->getRules();
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $messages = array_merge($messages, $rulesActionPlan['messages']);
        return Validator::make($request->all(), $rules, $messages)->validate();
    }
}
