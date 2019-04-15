<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LegalAspects\Contracts\ContractRequest;
use App\Http\Requests\LegalAspects\Contracts\ContractCompleteInfoRequest;
use App\Http\Requests\LegalAspects\Contracts\ListCheckItemsContractRequest;
use App\Models\LegalAspects\ContractLesseeInformation;
use App\Models\LegalAspects\SectionCategoryItems;
use App\Models\LegalAspects\Qualifications;
use App\Models\LegalAspects\ItemQualificationContractDetail;
use App\Models\LegalAspects\FileUpload;
use App\User;
use App\Models\Role;
use App\Traits\UserTrait;
use Session;
use DB;
use Validator;
use Carbon\Carbon;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractLesseeController extends Controller
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
            $items->transform(function($item, $index){
                $item['activities_defined'] = $item->activities()->pluck("description");
                return $item;
            });
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
            } else {
                foreach ($itemsCalificated as $item) {
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
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        //Proceso de planes de acción
        try {
            //Pendiente preguntarle a ander yo como se cual plan de accion es de un contratista por la actividad definda
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        // $user = User::find(Auth::user()->id);
        // $contract_id = $user->contractInfo[0]->id;
        // foreach ($request->items_calificated as $items) {
        //     foreach ($items as $item) {
        //         $item['item_id'] = $item['id'];
        //         $item['qualification_id'] = $item['qualification'];
        //         $item['contract_id'] = $contract_id;
        //         $user->itemsCalificatedContract()->sync($item);
        //     }
        // }
        // \Log::info($request);
        DB::commit();
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
    public function rulesListContract($request)
    {
        // Como el request->items viene como string se pierden los archivos, entonces--
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
            // 'items.*.actionPlan.activities.*.responsible_id' => 'required', Pendiente por que en local no se listan los responsables
            'items.*.actionPlan.activities.*.state' => 'required',
            'items.*.files.*.name' => 'required',
            'items.*.files.*.file' => 'required|file|mimes:pdf'
        ];

        $rulesActionPlan = ActionPlan::prefixIndex('items.*.actionPlan.activities.*.')->getRules();
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $messages = array_merge($messages, $rulesActionPlan['messages']);
        return Validator::make($request->all(), $rules, $messages)->validate();
    }
}
