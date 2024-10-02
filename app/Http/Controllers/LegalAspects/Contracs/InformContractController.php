<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\LegalAspects\Contracts\InformContractRequest;
use App\Models\LegalAspects\Contracts\Inform;
use App\Models\LegalAspects\Contracts\InformContract;
use App\Models\LegalAspects\Contracts\InformThemeItem;
use App\Models\LegalAspects\Contracts\InformContractItem;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\InformContractItemObservation;
use App\Models\LegalAspects\Contracts\InformContractItemFile;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use App\Traits\ContractTrait;
use App\Models\General\Company;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;

class InformContractController extends Controller
{
    use Filtertrait;
    use ContractTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:contracts_evaluations_perform_evaluation, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_evaluations_view_evaluations_made, {$this->team}", ['except' => ['report', 'getTotales', 'exportReport', 'store', 'getData']] );
        $this->middleware("permission:contracts_evaluations_edit_evaluations_made, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_evaluations_report_view, {$this->team}", ['only' => ['report', 'getTotales']]);
        $this->middleware("permission:contracts_evaluations_report_export, {$this->team}", ['only' => 'exportReport']);*/
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
        $inform_contracts = InformContract::select(
                'sau_ct_inform_contract.*',
                'sau_ct_information_contract_lessee.social_reason as social_reason',
                'sau_ct_information_contract_lessee.nit as nit',
                'sau_users.name as name',
                DB::raw("CONCAT(year, ' - ', month) AS periodo"),
                'sau_ct_proyects.name AS proyects'
            )
            ->join('sau_users', 'sau_users.id', 'sau_ct_inform_contract.evaluator_id')
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_inform_contract.contract_id')
            ->leftJoin('sau_ct_proyects', 'sau_ct_proyects.id', 'sau_ct_inform_contract.proyect_id')
            ->where('sau_ct_inform_contract.inform_id', '=', $request->get('modelId'))
            ->orderBy('sau_ct_inform_contract.id', 'DESC');

        if ($this->user->hasRole('Contratista', $this->team))
        {
            $contract = $this->getContractUser($this->user->id, $this->company);

            $inform_contracts->where('sau_ct_inform_contract.contract_id', $contract->id);
        }

        /*$url = "/legalaspects/inform/contracts/".$request->get('modelId');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (isset($filters["dateRange"]) && $filters["dateRange"])
        {
            $dates_request = explode('/', $filters["dateRange"]);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }
            
            $inform_contracts->betweenDate($dates);
        }*/

        //return Vuetable::of($inform_contracts)->make();
        return Vuetable::of($inform_contracts)
                    ->make();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\InformContracts\InformContractRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(InformContractRequest $request)
    {
        $valid = InformContract::where('contract_id', $request->contract_id)
            ->where('year', $request->year)
            ->where('inform_id', $request->inform_id)
            ->where('month', $request->month);

        if ($this->proyectContract == 'SI')
            $valid->where('proyect_id', $request->proyect_id);
        
        $valid = $valid->exists();

        if ($valid)
        {
            return $this->respondWithError('Este período ya ha sido evaluado para este contratista, por favor seleccione otro período');
        }

        Validator::make($request->all(), [
            "inform.themes.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg' &&
                        $value->getClientOriginalExtension() != 'pdf' &&
                        $value->getClientOriginalExtension() != 'xlsx' &&
                        $value->getClientOriginalExtension() != 'xls' &&
                        $value->getClientOriginalExtension() != 'docx' &&
                        $value->getClientOriginalExtension() != 'doc' &&
                        $value->getClientOriginalExtension() != 'ppt' &&
                        $value->getClientOriginalExtension() != 'pptx')
                        
                        $fail('Archivo debe ser png, jpg, jpeg, pdf, doc, docx, xlsx, xls, ppt, pptx');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $inform_contract = new InformContract($request->all());
            $inform_contract->company_id = $this->company;
            $inform_contract->inform_date = date('Y-m-d H:i:s');
            $inform_contract->evaluator_id = $this->user->id;

            if ($this->proyectContract == 'SI')
                $inform_contract->proyect_id = $request->proyect_id;
            
            if(!$inform_contract->save()){
                return $this->respondHttp500();
            }        

            $this->saveResults($inform_contract, $request->get('inform'));

            $this->deleteData($request->get('delete'));
            
            $this->saveLogActivitySystem('Contratistas - Informes Mensuales', 'Se realizo un informe '.$inform_contract->inform->name .'al contratista '.$inform_contract->contract->social_reason.' en el mes '.$inform_contract->month.' en el año '.$inform_contract->year);

            DB::commit();

            $data = $this->getInformData($inform_contract->id);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se realizo la evaluación del informe mensual'
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\InformContracts\InformContractRequest $request
     * @param  App\LegalAspects\InformContract $inform_contract
     * @return \Illuminate\Http\Response
     */
    public function update(InformContractRequest $request, InformContract $informContract)
    {
        Validator::make($request->all(), [
            "inform.themes.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg' &&
                        $value->getClientOriginalExtension() != 'pdf' &&
                        $value->getClientOriginalExtension() != 'xlsx' &&
                        $value->getClientOriginalExtension() != 'xls' &&
                        $value->getClientOriginalExtension() != 'docx' &&
                        $value->getClientOriginalExtension() != 'doc' &&
                        $value->getClientOriginalExtension() != 'ppt' &&
                        $value->getClientOriginalExtension() != 'pptx')
                        
                        $fail('Archivo debe ser png, jpg, jpeg, pdf, doc, docx, xlsx, xls, ppt, pptx');
                },
            ]
        ])->validate();
        
        DB::beginTransaction();

        try
        {
            $informContract->fill($request->all());

            if ($this->proyectContract == 'SI')
                $informContract->proyect_id = $request->proyect_id;

            if(!$informContract->update()){
                return $this->respondHttp500();
            }

            $this->saveResults($informContract, $request->get('inform'));

            $this->deleteData($request->get('delete'));
            
            $this->saveLogActivitySystem('Contratistas - Informes Mensuales', 'Se realizo un informe '.$informContract->inform->name .'al contratista '.$informContract->contract->social_reason.' en el mes '.$informContract->month.' en el año '.$informContract->year);

            DB::commit();

            $data = $this->getInformData($informContract->id);

            /*if ($informContract->ready())
                $this->sendNotification($informContract->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se actualizo la evaluación del informe mensual'
        ]);
    }

    private function saveResults($informContract, $inform)
    {
        $informContract->results()->delete();

        ActionPlan::
                user($this->user)
            ->module('contracts')
            ->url(url('/administrative/actionplans'));

        foreach ($inform['themes'] as $objective)
        {
            foreach ($objective['items'] as $item)
            {
                $itemModel = InformThemeItem::find($item['id']);

                $itemEvaluation = InformContractItem::firstOrCreate(
                [
                    'inform_id' => $informContract->id,
                    'item_id' => $item['id'],
                    'value_programed' => isset($item['programmed']) ? $item['programmed'] : NULL,
                    'value_executed' => isset($item['executed']) ? $item['executed'] : NULL,
                    'compliance' => isset($item['compliance']) ? $item['compliance'] : NULL
                ], 
                [
                    'inform_id' => $informContract->id,
                    'item_id' => $item['id'],
                    'value_programed' => isset($item['programmed']) ? $item['programmed'] : NULL,
                    'value_executed' => isset($item['executed']) ? $item['executed'] : NULL,
                    'compliance' => isset($item['compliance']) ? $item['compliance'] : NULL

                ]);
                
                foreach ($item['observations'] as $observation)
                {
                    $id = isset($observation['id']) ? $observation['id'] : NULL;
                    $informContract->observations()->updateOrCreate(['id'=>$id], $observation);
                }

                if ($item['files'] && COUNT($item['files']) > 0)
                {
                    $files_names_delete = [];

                    foreach ($item['files'] as $keyF => $file) 
                    {
                        $create_file = true;

                        if (isset($file['id']))
                        {
                            $fileUpload = InformContractItemFile::findOrFail($file['id']);

                            if ($file['old_name'] == $file['file'])
                                $create_file = false;
                            else
                                array_push($files_names_delete, $file['old_name']);
                        }
                        else
                        {
                            $fileUpload = new InformContractItemFile();
                            $fileUpload->item_id = $itemModel->id;
                            $fileUpload->inform_id = $informContract->id;
                        }

                        if ($create_file)
                        {
                            $file_tmp = $file['file'];
                            $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
                            $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                            $fileUpload->file = $nameFile;
                            $fileUpload->name_file = $file_tmp->getClientOriginalName();
                            $fileUpload->type_file = $file_tmp->getClientOriginalExtension();
                        }

                        if (!$fileUpload->save())
                            return $this->respondHttp500();
                    }

                    //Borrar archivos reemplazados
                    foreach ($files_names_delete as $keyf => $file)
                    {
                        Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
                    }
                }

                $detail_procedence = 'Contratistas - Informes Mensuales Contratista. Informe: ' . $inform['name'] . ' - Tema: ' .  $objective['description'] . ' - Item: ' . $item['description'];
            
                if (isset($item['actionPlan']))
                {
                    ActionPlan::
                    model($itemEvaluation)
                    ->detailProcedence($detail_procedence)
                    ->activities($item['actionPlan'])
                    ->save();
                }                
            }
        }

        $state = InformContractItem::where('inform_id', $informContract->id)->whereNull('value_executed')->get();

        if (COUNT($state) > 0)
            $informContract->update(['state' => 'En proceso']);
        else
            $informContract->update(['state' => 'Terminada']);

        ActionPlan::sendMail();
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
            $informContract = $this->getInformData($id);

            return $this->respondHttp200([
                'data' => $informContract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getInformData($id)
    {
        $informContract = InformContract::findOrFail($id);
        $informContract->multiselect_contract_id = $informContract->contract->multiselect(); 

        $informContract->multiselect_proyect = $informContract->proyect_id && $informContract->proyect_id > 0 ? $informContract->proyect->multiselect() : NULL; 
        
        $inform_base = $this->getInform($informContract->inform_id);
        $informContract->inform = $this->setValuesInform($informContract, $inform_base);

        $informContract->delete = [
            'observations' => [],
            'files' => []
        ];

        return $informContract;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getData($id)
    {
        try
        {
            $informContract = new InformContract;
            $informContract->contract_id = '';
            $informContract->inform_id = $id;
            $informContract->proyect_id = $id;
            $informContract->observation = '';
            $informContract->Objective_inform = '';
            $informContract->year = '';
            $informContract->month = '';
            $informContract->inform = $this->getInform($id);
        
            $informContract->delete = [
                'observations' => [],
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $informContract,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    private function getInform($id)
    {
        $inform = Inform::findOrFail($id);

        foreach ($inform->themes as $objective)
        {
            $objective->key = Carbon::now()->timestamp + rand(1,10000);

            foreach ($objective->items as $subobjective)
            {
                $subobjective->key = Carbon::now()->timestamp + rand(1,10000);
                $subobjective->observations = [];
                $subobjective->files = [];
            }
        }

        return $inform;
    }

    private function setValuesInform($informContract, $inform_base)
    {
        $inform = Inform::find($informContract->inform_id);

        foreach ($inform_base->themes as $objective)
        {
            foreach ($objective ->items as $item)
            {                
                $item->observations = $informContract->observations()->where('item_id', $item->id)->get();
                $files = $informContract->files()->where('item_id', $item->id)->get();

                $images_pdf = [];
                $count_pdf = 0;
                $i = 0;
                $j = 0;

                $files->transform(function($file, $indexFile) use (&$i, &$j, &$images_pdf, &$count_pdf, $item) {
                    $file->key = Carbon::now()->timestamp + rand(1,10000);
                    $file->type_file = $file->type_file;
                    $file->name_file = $file->name_file;
                    $file->old_name = $file->file;
                    $file->path = $file->path_image();
                    $images_pdf[$i][$j] = ['file' => $file->path, 'type' => $file->type_file, 'name' => $file->name_file];
                    $j++;

                    if ($item->show_program_value == 'SI')
                    {
                        if ($j > 3)
                        {
                            $i++;
                            $j = 0;
                        }
                    }
                    else
                    {
                        if ($j > 1)
                        {
                            $i++;
                            $j = 0;
                        }
                    }

                    if ($file->type_file == 'pdf')
                        $count_pdf++;

                    return $file;
                });

                $item->files = $files;
                $item->files_pdf = $images_pdf;
                $item->count_file_pdf = $count_pdf;

                $values = $informContract->results()->where('item_id', $item->id)->get();

                foreach ($values as $key => $value) {
                    $item->programmed = $value->value_programed;
                    $item->executed = $value->value_executed;
                    $item->compliance = $value->compliance;
                }

                $informContractItem = InformContractItem::where('inform_id', $informContract->id)->where('item_id',  $item->id)->first();

                if ($informContractItem)
                    $item->actionPlan = ActionPlan::model($informContractItem)->prepareDataComponent();
                else
                    $item->actionPlan = [
                        "activities" => [],
                        "activitiesRemoved" => []
                    ];
            }
        }

        return $inform_base;
    }

    private function deleteData($data)
    {
        if (COUNT($data['observations']) > 0)
            InformContractItemObservation::destroy($data['observations']);
        
        if (COUNT($data['files']) > 0)
        {
            foreach ($data['files'] as $keyF => $file)
            {
                $file_delete = InformContractItemFile::find($file);

                if ($file_delete)
                {
                    Storage::disk('s3')->delete($file_delete->path_client(false)."/".$file_delete->file);
                    $file_delete->delete();
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(InformContract $informContract)
    {
        DB::beginTransaction();

        try
        { 
            foreach ($informContract->items as $item)
            {  
                ActionPlan::model($item)->modelDeleteAll();
            }

            foreach ($informContract->files as $file)
            {
                $file_delete = InformContractItemFile::find($file->id);

                if ($file_delete)
                {
                    /*foreach ($file_delete as $file_2) 
                    {*/
                        Storage::disk('s3')->delete($file_delete->path_client(false)."/".$file_delete->file);
                        $file_delete->delete();
                    //}
                }
            }

            $this->saveLogDelete('Contratistas - Informes mensuales', 'Se elimino el informe '.$informContract->inform->name.' realizada al contratista '.$informContract->contract->social_reason.' en el mes '.$informContract->month.' en el año '.$informContract->year);

            if(!$informContract->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la evaluación del informe mensual'
        ]);
    }

    public function downloadFile(InformContractItemFile $informContractItemFile)
    {
        $name = $informContractItemFile->name_file;
        if ($name)
            return Storage::disk('s3')->download($informContractItemFile->path_donwload(), $name);
        else
            return Storage::disk('s3')->download($informContractItemFile->path_donwload());
    }

    public function downloadPdf(InformContract $informContract)
    {
        $inform = $this->getDataExportPdf($informContract->id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.informContract', ['inform' => $inform] );

        $pdf->setPaper('A3', 'landscape');

        return $pdf->download('informe_mensual.pdf');
    }

    public function getDataExportPdf($id)
    {
        $informContract = InformContract::findOrFail($id);
        $informContract->evaluator;
        $informContract->contract;
        $informContract->proyect;
        $inform_base = $this->getInform($informContract->inform_id);

        $informContract->inform = $this->setValuesInform($informContract, $inform_base);

        $company = Company::select('logo')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $informContract->logo = $logo;
        $informContract->inform_base = $inform_base;

        return $informContract;
    }

    public function multiselectYears()
    {
        $year_act = Carbon::now()->format('Y');
        $year_ant = Carbon::now()->subYears(1)->format('Y');
        $years = [
            $year_ant => $year_ant,
            $year_act => $year_act
        ];

        arsort($years);

        return $this->multiSelectFormat(collect($years));
    }

    public function multiselectMonth()
    {
        $months = [
            "Enero" => "Enero",
            "Febrero" => "Febrero",
            "Marzo" => "Marzo",
            "Abril" => "Abril",
            "Mayo" => "Mayo",
            "Junio" => "Junio",
            "Julio" => "Julio",
            "Agosto" => "Agosto",
            "Septiembre" => "Septiembre",
            "Octubre" => "Octubre",
            "Noviembre" => "Noviembre",
            "Diciembre" => "Diciembre",
        ];

        return $this->multiSelectFormat(collect($months));
    }

    public function periodExist(Request $request)
    {
        $valid = InformContract::where('contract_id', $request->contract)
        ->where('year', $request->year)
        ->where('month', $request->month)
        ->where('inform_id', $request->inform);

        if ($this->proyectContract == 'SI')
            $valid->where('proyect_id', $request->proyect_id);

        $valid = $valid->exists();

        if ($valid)
            return $this->respondWithError('Este período ya ha sido evaluado para este contratista, por favor seleccione otro período');
        else
            return $this->respondHttp200([
            'message' => 'Período valido'
        ]);
    }

    public function historyItemQualification(Request $request)
    {
        $headingsXls = collect([]);

        $months = $this->multiselectMonth();

        foreach ($months as $key => $month) 
        {
            $headingsXls->push(['id' => $month['name'], 'name' => $month['name']]);
        }

        $headingsXls->push(['id' => 'Acumulado', 'name' => 'Acumulado']);

        $headings = $headingsXls->pluck('name')->toArray();

        $qualifications = InformContractItem::select('sau_ct_inform_contract.*',
        'sau_ct_inform_contract_items.*')
        ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
        ->where('sau_ct_inform_contract_items.item_id', $request->item_id)
        ->where('sau_ct_inform_contract.year', $request->year)
        ->where('sau_ct_inform_contract.inform_id', $request->inform)
        ->where('contract_id', $request->contract);

        if ($this->proyectContract == 'SI')
            $qualifications->where('sau_ct_inform_contract.proyect_id', $request->proyect_id);

        $qualifications = $qualifications->get();

        $answers = collect([]);
        $acumulado = 0;

        foreach ($headingsXls as $key => $item)
        {
            if ($item['id'] != 'Acumulado')
            {
                $response = $qualifications->where('month', $item['id'])->first();

                if ($response && $response != null)
                {
                    $answers->push($response->value_executed);
                    $acumulado = $acumulado + $response->value_executed;
                }
                else
                {
                    $answers->push(0);
                    $acumulado = $acumulado + 0;
                }
            }
            else
            {
                $answers->push($acumulado);
            }

        }

        $data = [
            'headings' => $headings,
            'answers' => $answers
        ];

        return $data;
    }
}