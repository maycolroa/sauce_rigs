<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\ListCheckQualification;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\LegalAspects\Contracts\FileModuleState;
use App\Http\Requests\LegalAspects\Contracts\ListCheckItemsRequest;
use App\Jobs\LegalAspects\Contracts\ListCheck\ListCheckQualificationCopyJob;
use App\Http\Requests\LegalAspects\Contracts\ListCheckQualificationRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Illuminate\Support\Facades\Storage;
use Validator;
use Carbon\Carbon;
use App\Traits\ContractTrait;
use DB;
use PDF;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;

class ListCheckQualificationController extends Controller
{
    use ContractTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_list_standards_qualification_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_list_standards_qualification_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:contracts_list_standards_qualification_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_list_standards_qualification_d, {$this->team}", ['only' => 'destroy']);
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
        $contract = $this->getContractUser($this->user->id, $this->company);

        $qualifications = ListCheckQualification::select(
            'sau_ct_list_check_qualifications.*',
            DB::raw("case when sau_ct_list_check_qualifications.state is true then 'ACTIVA' else 'INACTIVA' end as state_list"),
            'sau_users.name as user_creator')
        ->join('sau_users', 'sau_users.id', 'sau_ct_list_check_qualifications.user_id')
        ->where('company_id', $this->company)
        ->where('contract_id', $contract->id)
        ->orderBy('sau_ct_list_check_qualifications.id', 'DESC');

        return Vuetable::of($qualifications)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ListCheckQualificationRequest $request)
    {
        $contract = $this->getContractUser($this->user->id, $this->company);

        $qualification_exist = ListCheckQualification::where('company_id', $this->company)
        ->where('contract_id', $contract->id)
        ->where('state', true)
        ->orderBy('created_at', 'DESC')
        ->first();

        $qualification = new ListCheckQualification($request->all());
        $qualification->company_id = $this->company;
        $qualification->contract_id = $contract->id;
        $qualification->user_id = $this->user->id;
        $qualification->state = true;
        
        if(!$qualification->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Contratistas - Lista de chequeo', 'Se creo la lista de chequeo para el periodo '.$qualification->validity_period);

        if($qualification_exist)
            $qualification_exist->update([
                'state' => false
            ]);

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
            $qualification = ListCheckQualification::findOrFail($id);

            return $this->respondHttp200([
                'data' => $qualification,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Regionals\RegionalRequest  $request
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function update(ListCheckQualificationRequest $request, ListCheckQualification $listCheck)
    {
        $listCheck->fill($request->all());
        
        if(!$listCheck->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Contratistas - Lista de chequeo', 'Se edito la lista de chequeo para el periodo '.$listCheck->validity_period);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el registro'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeRegional  $regional
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListCheckQualification $listCheck)
    {
        $qualification = ListCheckQualification::findOrFail($listCheck->id);

        $qualification_active = ListCheckQualification::where('contract_id', $qualification->contract_id)->where('state', true)->get();

        if ($qualification->state == false)
        {
            foreach ($qualification->files as $file) 
            {                    
                $file->delete();
                //Storage::disk('s3')->delete('legalAspects/files/'. $file->file);
            }

            $this->saveLogDelete('Contratistas - Lista de Chequeo', 'Se elimino la lista de chequeo del periodo '.$qualification->validity_period.' del contratista '.$qualification->contract->social_reason);

            if(!$qualification->delete())
                return $this->respondHttp500();
        }
        else
        {
            return $this->respondWithError('No se puede eliminar la calificación ya que debe tener por lo menos 1 calificación activa.');
        }
        
        
        return $this->respondHttp200([
            'message' => 'Se elimino el registro'
        ]);
    }

    public function getListCheckItems(Request $request)
    {
        try
        {
           $data = $this->getListCheckItemsPdf($request->id);

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function listCheckItemsClone(Request $request)
    {
        try
        {   
            $data = $this->listCheckQualificationCopy($request->id);

            return $this->respondHttp200([
                'data' => $data
            ]);

        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function listCheckQualificationCopy($list_selected)
    {
      DB::beginTransaction();

      try
      {
        $contract = $this->getContractUser($this->user->id);

        $items = $this->getStandardItemsContract($contract);

        $qualifications = Qualifications::pluck("name", "id");

        $qualification_exist = ListCheckQualification::find($list_selected);

        $newQualification = new ListCheckQualification();
        $newQualification->validity_period = $qualification_exist->validity_period;
        $newQualification->company_id = $this->company;
        $newQualification->contract_id = $contract->id;
        $newQualification->user_id = $this->user->id;
        $newQualification->state = true;

        $qualification_exist_active = ListCheckQualification::where('company_id', $this->company)
        ->where('contract_id', $contract->id)
        ->where('state', true)
        ->orderBy('created_at', 'DESC')
        ->first();
        
        if(!$newQualification->save()){
            return $this->respondHttp500();
        }

        $qualification_exist_active->update([
            'state' => false
        ]);

        //Obtiene los items calificados
        $getQualifications = ItemQualificationContractDetail::
            where('contract_id', $contract->id)
            ->where('list_qualification_id', $qualification_exist->id)
            ->get();

        /**Insercion de registros encontrados*/

        if (COUNT($getQualifications) > 0)
        {
          $getQualifications->each(function($item, $index) use ($qualifications, $newQualification, $contract) {

            $qualificationNewCopy = $item->replicate()->fill([
                'contract_id' => $contract->id,
                'list_qualification_id' => $newQualification->id
            ]);

            $qualificationNewCopy->save();     
          });
        }

        $data = $newQualification->id;

        DB::commit();

        return $data;

      } catch(Exception $e){
        \Log::info($e->getMessage());
        DB::rollback();
            $this->respondHttp500();
      }
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
            ],
            "items.*.files.*.name" => [
                function ($attribute, $value, $fail) 
                {
                    if ($value && is_string($value))
                    {
                        $exist = strpos($value, '/');

                        if ($exist)
                            $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                        else
                        {
                            $exist = strpos($value, '.');

                            if ($exist)
                                $fail('El nombre no puede contener ninguno de los caracteres especiales indicados');
                        }
                    }
                }
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
                $contract = ContractLesseeInformation::findOrFail($qualification_list->contract_id);
                //$contract = $this->getContractUser($this->user->id);

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

                if ($exist == 'SI')
                {
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
                            'list_qualification_id' => $request->list_qualification_id,
                            'state_aprove_qualification' => 'PENDIENTE',
                            'reason_rejection' => NULL
                        ]);
                    }
                else
                {
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
                            'list_qualification_id' => $request->list_qualification_id,
                            'reason_rejection' => NULL
                        ]);
                }
                
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
                                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
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
                        }

                        //Borrar archivos reemplazados
                        foreach ($files_names_delete as $keyf => $file)
                        {
                            //Storage::disk('s3')->delete('legalAspects/files/'. $file);
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
                        //Storage::disk('s3')->delete('legalAspects/files/'. $file['old_name']);
                    }
                }
            }

            ActionPlan::sendMail();

            DB::commit();

            $data['observations'] = $data['observations'] ? $data['observations'] : '';

            $this->reloadLiskCheckResumen($contract, $request->list_qualification_id);

            return $this->respondHttp200([
                'data' => $data
            ]);  

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            \Log::info($e->getTraceAsString());
            return $this->respondHttp500();
            
        }
    }

    /*public function listCheckQualificationCopy(Request $request)
    {
        $contract = $this->getContractUser($this->user->id);

        try
        {
            ListCheckQualificationCopyJob::dispatch($this->user, $this->company, $contract, $request->list_selected);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }*/

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
        $qualification_list = ListCheckQualification::findOrFail($id);

        if ($qualification_list->id)
            $contract = ContractLesseeInformation::findOrFail($qualification_list->contract_id);
        else 
            $contract = $this->getContractUser($this->user->id);

        $items = $this->getStandardItemsContract($contract);

        $qualifications = Qualifications::pluck("name", "id");

        try
        {
            $exist = ConfigurationsCompany::findByKey('validate_qualification_list_check');
            
        } catch (\Exception $e) {
            $exist = 'NO';
        }                         
        
        //Obtiene los items calificados
        $items_calificated = ItemQualificationContractDetail::
            where('contract_id', $contract->id)
        ->where('list_qualification_id', $qualification_list->id);

        /*if ($exist == 'SI')
            $items_calificated->where('state_aprove_qualification', 'APROBADA');*/

        $items_calificated = $items_calificated->pluck("qualification_id", "item_id");
                
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
                $item->state_aprove_qualification = isset($items_aprove[$item->id]) ? $items_aprove[$item->id] : NULL;
                        
                if (isset($items_reason_reject[$item->id]))
                    $item->reason_rejection = isset($items_reason_reject[$item->id]) ? $items_reason_reject[$item->id] : NULL;

                $item->files = [];
                $item->actionPlan = [
                    "activities" => [],
                    "activitiesRemoved" => []
                ];

                if ($item->qualification == 'NA')
                    $compliance['no_aplica']++;
                else if ($item->qualification == 'C')
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
                else
                    $compliance['no_cumple']++;

                $compliance['total']++;

                return $item;
            });

            $compliance['p_cumple'] = round(($compliance['cumple']/$compliance['total'])*100, 2);
            $compliance['p_no_aplica'] = round(($compliance['no_aplica']/$compliance['total'])*100, 2);
            $compliance['p_total'] = round((($compliance['cumple']+$compliance['no_aplica'])/$compliance['total'])*100, 2);


            if ($compliance['no_cumple'] > 0)
            {
                $compliance['p_no_cumple']  = $compliance['total']-$compliance['cumple']-$compliance['no_aplica'];
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
                'cumplimiento' => $compliance,
                'validate_qualification' => $exist
            ];
        }
        else
            $data = [];

        return  $data;
    }

     public function toggleState(Request $request, $id)
    {
        $qualification = ListCheckQualification::findOrFail($id);

        $qualification_active = ListCheckQualification::where('contract_id', $qualification->contract_id)->where('state', true)->get();

        if ($qualification->state == false)
        {
            $qualification->state = true;

            if ($qualification_active->count() >= 1)
            {
                foreach ($qualification_active as $value) {
                    $value->state = false;
                    $value->update();
                }
            }
        }
        else if ($qualification_active->count() > 1)
        {
            if ($qualification->state == true)
                $qualification->state = false;
            else
                $qualification->state = true;
        }
        else
        {
            return $this->respondWithError('No se puede desactivar la calificación ya que debe tener por lo menos 1 calificación activa.');
        }

        if (!$qualification->update()) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado de la calificación'
        ]);
    }

    public function verifyRequiredFile(Request $request)
    {
        $record = SectionCategoryItems::find($request->item_id);

        if ($record)
        {
            $record =  $record->itemStandardCompany($this->company)->first();

            if ($record && $record->pivot->required == 'SI')
                return $data = 'Requerido';
            else
                return $data = 'No Requerido';
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
}
