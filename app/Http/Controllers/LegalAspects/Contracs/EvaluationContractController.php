<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\LegalAspects\Contracts\EvaluationContractRequest;
use App\Models\LegalAspects\Contracts\Evaluation;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\Interviewee;
use App\Models\LegalAspects\Contracts\Item;
use App\Models\LegalAspects\Contracts\Observation;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\LegalAspects\Contracts\EvaluationContractItem;
use App\Models\General\Module;
use App\Models\LegalAspects\Contracts\TypeRating;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationContractReportExportJob;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationExportJob;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationSendNotificationJob;
use App\Inform\LegalAspects\Contract\Evaluations\InformManagerEvaluationContract;
use App\Traits\Filtertrait;
use App\Models\General\Company;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;

class EvaluationContractController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_evaluations_perform_evaluation, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_evaluations_view_evaluations_made, {$this->team}", ['except' => ['report', 'getTotales', 'exportReport', 'store', 'getData']] );
        $this->middleware("permission:contracts_evaluations_edit_evaluations_made, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_evaluations_report_view, {$this->team}", ['only' => ['report', 'getTotales']]);
        $this->middleware("permission:contracts_evaluations_report_export, {$this->team}", ['only' => 'exportReport']);
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
        $evaluation_contracts = EvaluationContract::select(
                'sau_ct_evaluation_contract.*',
                'sau_ct_information_contract_lessee.social_reason as social_reason',
                'sau_ct_information_contract_lessee.nit as nit',
                'sau_users.name as name'
            )
            ->join('sau_users', 'sau_users.id', 'sau_ct_evaluation_contract.evaluator_id')
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id')
            /*->join('sau_ct_evaluations', 'sau_ct_evaluations.id', 'sau_ct_evaluation_contract.evaluation_id')
            ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
            ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
            ->groupBy('sau_ct_evaluation_contract.id')*/;

        if ($request->has('modelId') && $request->get('modelId'))
            $evaluation_contracts->where('sau_ct_evaluation_contract.evaluation_id', '=', $request->get('modelId'));
        else 
        {
            $evaluation_contracts->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.information_id', 'sau_ct_evaluation_contract.contract_id');
            $evaluation_contracts->where('sau_user_information_contract_lessee.user_id', '=', $this->user->id);
        }

        $url = "/legalaspects/evaluations/contracts/".$request->get('modelId');

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
            
            $evaluation_contracts->betweenDate($dates);
        }

        return Vuetable::of($evaluation_contracts)
                    ->addColumn('retrySendMail', function ($evaluation_contract) {
                        return $evaluation_contract->ready();
                    })
                    ->make();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\EvaluationContracts\EvaluationContractRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EvaluationContractRequest $request)
    {
        Validator::make($request->all(), [
            "evaluation.objectives.*.subobjectives.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg' &&
                        $value->getClientOriginalExtension() != 'pdf')
                        
                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $evaluation_contract = new EvaluationContract($request->all());
            $evaluation_contract->company_id = $this->company;
            $evaluation_contract->evaluation_date = date('Y-m-d H:i:s');
            $evaluation_contract->evaluator_id = $this->user->id;
            
            if(!$evaluation_contract->save()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluation_contract->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                $evaluation_contract->interviewees()->createMany($request->get('interviewees'));
            }

            $this->saveResults($evaluation_contract, $request->get('evaluation'));

            $this->deleteData($request->get('delete'));

            DB::commit();

            $data = $this->getEvaluationData($evaluation_contract->id);

            /*if ($evaluation_contract->ready())
                $this->sendNotification($evaluation_contract->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se realizo la evaluación'
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\EvaluationContracts\EvaluationContractRequest $request
     * @param  App\LegalAspects\EvaluationContract $evaluation_contract
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluationContractRequest $request, EvaluationContract $evaluationContract)
    {
        Validator::make($request->all(), [
            "evaluation.objectives.*.subobjectives.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                    $value->getClientMimeType() != 'image/png' && 
                    $value->getClientMimeType() != 'image/jpg' &&
                    $value->getClientMimeType() != 'image/jpeg' &&
                    $value->getClientOriginalExtension() != 'pdf')
                    
                    $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();
        
        DB::beginTransaction();

        try
        {
            $evaluationContract->fill($request->all());

            if(!$evaluationContract->update()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluationContract->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }
            else
            {
                $evaluationContract->evaluators()->sync([]);
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                foreach ($request->get('interviewees') as $interviewee)
                {    
                    $id = isset($interviewee['id']) ? $interviewee['id'] : NULL;
                    $evaluationContract->interviewees()->updateOrCreate(['id'=>$id], $interviewee);
                }
            }

            $this->saveResults($evaluationContract, $request->get('evaluation'));
            
            $evaluationContract->histories()->create([
                'user_id' => $this->user->id
            ]);

            $this->deleteData($request->get('delete'));

            DB::commit();

            $data = $this->getEvaluationData($evaluationContract->id);

            /*if ($evaluationContract->ready())
                $this->sendNotification($evaluationContract->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se actualizo la evaluación'
        ]);
    }

    private function saveResults($evaluationContract, $evaluation)
    {
        $evaluationContract->results()->delete();

        ActionPlan::
                user($this->user)
            ->module('contracts')
            ->url(url('/administrative/actionplans'));

        foreach ($evaluation['objectives'] as $objective)
        {
            foreach ($objective['subobjectives'] as $subobjective)
            {
                foreach ($subobjective['items'] as $item)
                {
                    $itemModel = Item::find($item['id']);
                    
                    foreach ($item['ratings'] as $rating)
                    {
                        $value = $rating['apply'] == 'NO' ? null : ($rating['value'] ? $rating['value'] : 'pending');
                        $rating['value'] = $value;

                        $evaluationContract->results()->updateOrCreate(['item_id'=>$itemModel->id, 'type_rating_id'=>$rating['type_rating_id']], $rating);
                    }

                    foreach ($item['observations'] as $observation)
                    {
                        $id = isset($observation['id']) ? $observation['id'] : NULL;
                        $evaluationContract->observations()->updateOrCreate(['id'=>$id], $observation);
                    }

                    if ($item['files'] && COUNT($item['files']) > 0)
                    {
                        $files_names_delete = [];

                        foreach ($item['files'] as $keyF => $file) 
                        {
                            $create_file = true;

                            if (isset($file['id']))
                            {
                                $fileUpload = EvaluationFile::findOrFail($file['id']);

                                if ($file['old_name'] == $file['file'])
                                    $create_file = false;
                                else
                                    array_push($files_names_delete, $file['old_name']);
                            }
                            else
                            {
                                $fileUpload = new EvaluationFile();
                                $fileUpload->item_id = $itemModel->id;
                                $fileUpload->evaluation_id = $evaluationContract->id;
                            }

                            if ($create_file)
                            {
                                $file_tmp = $file['file'];
                                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                                $fileUpload->file = $nameFile;
                                $fileUpload->name_file = $file_tmp->getClientOriginalName();
                                $fileUpload->type_file = $file_tmp->extension();
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

                    $itemEvaluation = EvaluationContractItem::firstOrCreate(
                        [
                            'evaluation_id' => $evaluationContract->id,
                            'item_id' => $item['id']
                        ], 
                        [
                            'evaluation_id' => $evaluationContract->id,
                            'item_id' => $item['id']
                        ]);

                    $detail_procedence = 'Contratistas - Evaluación Contratista. Evaluación: ' . $evaluation['name'] . ' - Tema: ' .  $objective['description'] . '- Subtema: ' . $subobjective['description'] . ' - Item: ' . $item['description'];

                    ActionPlan::
                        model($itemEvaluation)
                        ->detailProcedence($detail_procedence)
                        ->activities($item['actionPlan'])
                        ->save();
                }
            }
        }

        $state = $evaluationContract->results()->where('sau_ct_evaluation_item_rating.value', 'pending')->get();

        if (COUNT($state) > 0)
            $evaluationContract->update(['state' => 'En proceso']);
        else
            $evaluationContract->update(['state' => 'Terminada']);

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
            $evaluationContract = $this->getEvaluationData($id);

            return $this->respondHttp200([
                'data' => $evaluationContract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getEvaluationData($id)
    {
        $evaluationContract = EvaluationContract::findOrFail($id);
        $evaluationContract->multiselect_contract_id = $evaluationContract->contract->multiselect();
        
        $evaluators_id = [];

        foreach ($evaluationContract->evaluators as $key => $value)
        {
            array_push($evaluators_id, $value->multiselect());
        }

        $evaluationContract->evaluators_id = $evaluators_id;
        $evaluationContract->multiselect_evaluators_id = $evaluators_id;

        $evaluationContract->interviewees;
        
        $evaluation_base = $this->getEvaluation($evaluationContract->evaluation_id);
        $evaluationContract->evaluation = $this->setValuesEvaluation($evaluationContract, $evaluation_base);

        $evaluationContract->delete = [
            'interviewees' => [],
            'observations' => [],
            'files' => []
        ];

        return $evaluationContract;
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
            $evaluationContract = new EvaluationContract;
            $evaluationContract->contract_id = '';
            $evaluationContract->evaluation_id = $id;
            $evaluationContract->evaluators_id = [];
            $evaluationContract->interviewees = [];
            $evaluationContract->observation = '';
            $evaluationContract->evaluation = $this->getEvaluation($id);
        
            $evaluationContract->delete = [
                'interviewees' => [],
                'observations' => [],
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $evaluationContract,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    private function getEvaluation($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $types = [];

        foreach ($evaluation->ratingsTypes as $rating)
        {
            array_push($types, [
                'id' => $rating->id,
                'type_rating_id' => $rating->id,
                'name' => $rating->name,
                'apply' => 'SI'
            ]);
        }

        $evaluation->types_rating = $types;

        foreach ($evaluation->objectives as $objective)
        {
            $objective->key = Carbon::now()->timestamp + rand(1,10000);

            foreach ($objective->subobjectives as $subobjective)
            {
                $subobjective->key = Carbon::now()->timestamp + rand(1,10000);

                foreach ($subobjective->items as $item)
                {
                    $item->key = Carbon::now()->timestamp + rand(1,10000);

                    $item_types = [];

                    foreach ($item->ratingsTypes as $rating)
                    {
                        $item_types[$rating->id]['type_rating_id'] = $rating->id;
                        $item_types[$rating->id]['item_id'] = $item->id;
                        $item_types[$rating->id]['apply'] = $rating->pivot->apply;
                        $item_types[$rating->id]['value'] = NULL;
                    }

                    $item->ratings = $item_types;
                    $item->observations = [];
                    $item->files = [];

                    $item->actionPlan = [
                        "activities" => [],
                        "activitiesRemoved" => []
                    ];
                }
            }
        }

        return $evaluation;
    }

    private function setValuesEvaluation($evaluationContract, $evaluation_base)
    {
        $evaluation = Evaluation::find($evaluationContract->evaluation_id);
        $report = [];
        $report_total = [];

        foreach ($evaluation->ratingsTypes()->get() as $key => $value)
        {
            $report[$value->id] = [
                'total' => 0,
                'total_c' => 0,
                'percentage' =>0
            ];

            $report_total[$value->id] = [
                'total' => 0,
                'total_c' => 0,
                'percentage' =>0,
                'category' => $value->name
            ];
        }

        $totals_apply = 0;

        foreach ($evaluation_base->types_rating as $key => $value)
        {
            if ($value['apply'] == 'SI')
                $totals_apply++;
        }

        foreach ($evaluation_base->objectives as $objective)
        {
            foreach ($objective->subobjectives as $subobjective)
            {
                $clone_report = $report;

                foreach ($subobjective->items as $item)
                {
                    $item->observations = $evaluationContract->observations()->where('item_id', $item->id)->get();
                    $files = $evaluationContract->files()->where('item_id', $item->id)->get();

                    $images_pdf = [];
                    $count_pdf = 0;
                    $i = 0;
                    $j = 0;

                    $files->transform(function($file, $indexFile) use ($totals_apply, &$i, &$j, &$images_pdf, &$count_pdf) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->type_file = $file->type_file;
                        $file->name_file = $file->name_file;
                        $file->old_name = $file->file;
                        $file->path = $file->path_image();
                        $images_pdf[$i][$j] = ['file' => $file->path, 'type' => $file->type_file, 'name' => $file->name_file];
                        $j++;

                        if ($j > ($totals_apply))
                        {
                            $i++;
                            $j = 0;
                        }

                        if ($file->type_file == 'pdf')
                            $count_pdf++;

                        return $file;
                    });

                    $item->files = $files;
                    $item->files_pdf = $images_pdf;
                    $item->count_file_pdf = $count_pdf;

                    $values = $evaluationContract->results()->where('item_id', $item->id)->pluck('value', 'type_rating_id');
                    $clone = $item->ratings;

                    foreach ($clone as $index => $rating)
                    {
                        $clone[$index]['value'] = isset($values[$index]) ? $values[$index] : NULL;

                        if ($rating['apply'] == 'SI')
                        {
                            if (!$clone[$index]['value'])
                            {
                                //$clone_report[$index]['total_c'] += 1;
                            }
                            else 
                            {
                                if ($clone[$index]['value'] == 'SI' /*|| $clone[$index]['value'] == 'N/A'*/)
                                {
                                    $clone_report[$index]['total'] += 1;
                                    $clone_report[$index]['total_c'] += 1;
                                }
                                else if ($clone[$index]['value'] == 'NO')
                                {
                                    $clone_report[$index]['total'] += 1;
                                }
                            }
                        }
                        /*else 
                        {
                            \Log::info('n/a');
                            $clone_report[$index]['total_c'] += 1;
                        }*/

                        if ($clone_report[$index]['total'] == 0)
                        {
                            $clone_report[$index]['percentage'] = 0;
                        }
                        else
                        {
                            $clone_report[$index]['percentage'] = round(($clone_report[$index]['total_c'] / $clone_report[$index]['total']) * 100, 1);
                        }
                    }

                    $item->ratings = $clone;

                    $evaluationContractItem = EvaluationContractItem::where('evaluation_id', $evaluationContract->id)->where('item_id',  $item->id)->first();

                    if ($evaluationContractItem)
                        $item->actionPlan = ActionPlan::model($evaluationContractItem)->prepareDataComponent();
                    else
                        $item->actionPlan = [
                            "activities" => [],
                            "activitiesRemoved" => []
                        ];
                }

                $subobjective->report = $clone_report;

                foreach ($clone_report as $key => $value)
                {
                    $report_total[$key]['total'] += $value['total'];
                    $report_total[$key]['total_c'] += $value['total_c'];
                }

                foreach ($report_total as $key => $value)
                {
                    if ($value['total'] == 0)
                    {
                        $report_total[$key]['percentage'] = 0;
                    }
                    else
                        $report_total[$key]['percentage'] = round(($value['total_c'] / $value['total']) * 100, 1);
                }
            }
        }

        //$evaluation_base->report_category = $report_total;
        $report_total = collect($report_total);
        $evaluation_base->report_total = $report_total->push([
            /*'total' => $report_total->sum('total'),
            'total_c' => $report_total->sum('total_c'),
            'percentage' => $report_total->sum('total') > 0 ? round(($report_total->sum('total_c') / $report_total->sum('total')) * 100, 1) : 0,*/
            'total' => count($report_total),
            'total_c' => $report_total->sum('percentage'),
            'percentage' => round($report_total->sum('percentage') / count($report_total), 1),
            'category' => 'Total'
        ]);

        return $evaluation_base;
    }

    private function deleteData($data)
    {
        if (COUNT($data['interviewees']) > 0)
            Interviewee::destroy($data['interviewees']);

        if (COUNT($data['observations']) > 0)
            Observation::destroy($data['observations']);
        
        if (COUNT($data['files']) > 0)
        {
            foreach ($data['files'] as $keyF => $file)
            {
                $file_delete = EvaluationFile::find($file);

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
    public function destroy(EvaluationContract $evaluationContract)
    {
        DB::beginTransaction();

        try
        { 
            foreach ($evaluationContract->items as $item)
            {  
                ActionPlan::model($item)->modelDeleteAll();
            }

            foreach ($evaluationContract->files  as $file)
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

            if(!$evaluationContract->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la evaluación realizada'
        ]);
    }

    public function report(Request $request)
    {
        $whereObjectives = '';
        $whereSubojectives = '';
        $whereDates = '';
        $whereQualificationTypes = '';
        $whereEvaluations = '';
        $whereItems = '';
        $whereContract = '';
        $subWhereQualificationTypes = '';

        $url = "/legalaspects/evaluations/report";
        
        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (isset($filters["evaluationsObjectives"]))
            $whereObjectives = $this->scopeQueryReport('o', $this->getValuesForMultiselect($filters["evaluationsObjectives"]), $filters['filtersType']['evaluationsObjectives']);

        if (isset($filters["evaluationsSubobjectives"]))
            $whereSubojectives = $this->scopeQueryReport('s', $this->getValuesForMultiselect($filters["evaluationsSubobjectives"]), $filters['filtersType']['evaluationsSubobjectives']);

        if (isset($filters["evaluationsEvaluations"]))
            $whereEvaluations = $this->scopeQueryReport('e', $this->getValuesForMultiselect($filters["evaluationsEvaluations"]), $filters['filtersType']['evaluationsEvaluations']);

        if (isset($filters["evaluationsItems"]))
            $whereItems = $this->scopeQueryReport('i', $this->getValuesForMultiselect($filters["evaluationsItems"]), $filters['filtersType']['evaluationsItems']);

        if (isset($filters["contracts"]))
            $whereContract = $this->scopeQueryReport('ec', $this->getValuesForMultiselect($filters["contracts"]), $filters['filtersType']['contracts'], 'contract_id');

        if (isset($filters["qualificationTypes"]))
        {
            $whereQualificationTypes = $this->scopeQueryReport('eir', $this->getValuesForMultiselect($filters["qualificationTypes"]), $filters['filtersType']['qualificationTypes'], 'type_rating_id');
            $subWhereQualificationTypes = $this->scopeQueryReport('etr', $this->getValuesForMultiselect($filters["qualificationTypes"]), $filters['filtersType']['qualificationTypes'], 'type_rating_id');
        }

        if (isset($filters["dateRange"]) && $filters["dateRange"])
        {
            $dates_request = explode('/', $filters["dateRange"]);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));

                $whereDates = " AND ec.evaluation_date BETWEEN '".$dates[0]."' AND '".$dates[1]."'";
            }
        }

        $evaluations = DB::table(DB::raw("(SELECT 
            t.*,
            CONCAT(ROUND( (t_cumple * 100) / (t_cumple + t_no_cumple), 1), '%') AS p_cumple,
            CONCAT(ROUND( (t_no_cumple * 100) / (t_cumple + t_no_cumple), 1), '%') AS p_no_cumple 
            FROM (
            
                SELECT 
                    GROUP_CONCAT(DISTINCT s.id) as id,
                    o.description as objective,
                    s.description as subobjective,
                    COUNT(DISTINCT ec.id) as t_evaluations,
                    SUM(IF(eir.value = 'NO' OR eir.value = 'pending', 1, 0)) AS t_no_cumple,
                    SUM(IF(eir.value = 'SI', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 0,
                                IF(eir.value IS NULL AND eir.item_id IS NULL,
                                    (SELECT 
                                            COUNT(etr.type_rating_id)
                                        FROM
                                            sau_ct_evaluation_type_rating etr
                                        WHERE
                                            etr.evaluation_id = e.id {$subWhereQualificationTypes}
                                    )
                                , 0)
                            )
                        )
                    ) AS t_cumple
                
                    FROM sau_ct_evaluation_contract ec
                    INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                    INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                    INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
                    INNER JOIN sau_ct_items i ON i.subobjective_id = s.id
                    LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id AND eir.evaluation_id = ec.id
                
                    WHERE ec.company_id = ".$this->company. $whereDates . $whereObjectives . $whereSubojectives . $whereQualificationTypes . $whereContract . $whereItems . $whereEvaluations ." 
                    GROUP BY objective, subobjective
                ) AS t
            ) AS t"));
    
        return Vuetable::of($evaluations)
            ->make();
    }

    private function scopeQueryReport($table, $data, $typeSearch, $primary = 'id', $specialColumn = false)
    {
        $ids = [];
        $query = '';

        foreach ($data as $key => $value)
        {
            $ids[] = $value;
        }

        if(COUNT($ids) > 0)
        {
            $ids = implode(",", $ids);

            if ($typeSearch == 'IN')
                $query = !$specialColumn ? " AND $table.$primary IN ($ids)" : " AND $specialColumn($table.$primary) IN ($ids)";

            else if ($typeSearch == 'NOT IN')
                $query = !$specialColumn ? " AND $table.$primary NOT IN ($ids)" : " AND $specialColumn($table.$primary) NOT IN ($ids)";
        }

        return $query;
    }

    /**
     * Export resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportReport(Request $request)
    {
        try
        {
            $evaluations = $this->getValuesForMultiselect($request->evaluationsEvaluations);
            $objectives = $this->getValuesForMultiselect($request->evaluationsObjectives);
            $subobjectives = $this->getValuesForMultiselect($request->evaluationsSubobjectives);
            $items = $this->getValuesForMultiselect($request->evaluationsItems);
            $contracts = $this->getValuesForMultiselect($request->contracts);
            $qualificationTypes = $this->getValuesForMultiselect($request->qualificationTypes);
            $dates = [];
            $filtersType = $request->filtersType;

            if (isset($request->dateRange) && $request->dateRange)
            {
                $dates_request = explode('/', $request->dateRange);

                if (COUNT($dates_request) == 2)
                {
                    array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                    array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
                }
                
            }

            $filters = [
                'objectives' => $objectives,
                'subobjectives' => $subobjectives,
                'qualificationTypes' => $qualificationTypes,
                'dates' => $dates,
                'filtersType' => $filtersType,
                'evaluations' => $evaluations,
                'items' => $items,
                'contracts' => $contracts
            ];

            EvaluationContractReportExportJob::dispatch($this->user, $this->company, $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function getTotales(Request $request)
    {
        $url = "/legalaspects/evaluations/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $whereDates = '';

        $objectives = !$init ? $this->getValuesForMultiselect($request->evaluationsObjectives) : (isset($filters['evaluationsObjectives']) ? $this->getValuesForMultiselect($filters['evaluationsObjectives']) : []);
        
        $subobjectives = !$init ? $this->getValuesForMultiselect($request->evaluationsSubobjectives) : (isset($filters['evaluationsSubobjectives']) ? $this->getValuesForMultiselect($filters['evaluationsSubobjectives']) : []);
        
        $qualificationTypes = !$init ? $this->getValuesForMultiselect($request->qualificationTypes) : (isset($filters['qualificationTypes']) ? $this->getValuesForMultiselect($filters['qualificationTypes']) : []);
        
        $evaluations = !$init ? $this->getValuesForMultiselect($request->evaluationsEvaluations) : (isset($filters['evaluationsEvaluations']) ? $this->getValuesForMultiselect($filters['evaluationsEvaluations']) : []);
        
        $items = !$init ? $this->getValuesForMultiselect($request->evaluationsItems) : (isset($filters['evaluationsItems']) ? $this->getValuesForMultiselect($filters['evaluationsItems']) : []);
        
        $contract = !$init ? $this->getValuesForMultiselect($request->contracts) : (isset($filters['contracts']) ? $this->getValuesForMultiselect($filters['contracts']) : []);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $whereObjectives = $this->scopeQueryReport('o', $objectives, $filtersType['evaluationsObjectives']);
        $whereSubojectives = $this->scopeQueryReport('s', $subobjectives, $filtersType['evaluationsSubobjectives']);
        $whereQualificationTypes = $this->scopeQueryReport('eir', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $subWhereQualificationTypes = $this->scopeQueryReport('etr', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $whereEvaluations = $this->scopeQueryReport('e', $evaluations, $filtersType['evaluationsEvaluations']);
        $whereItems = $this->scopeQueryReport('i', $items, $filtersType['evaluationsItems']);
        $whereContract = $this->scopeQueryReport('ec', $contract, $filtersType['contracts'], 'contract_id');

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));

                $whereDates = " AND ec.evaluation_date BETWEEN '".$dates[0]."' AND '".$dates[1]."'";
            }
        }

        $evaluations = DB::table(DB::raw("(SELECT 
            SUM(t_evaluations) AS evaluations,
            SUM(t_no_cumple) as t_no_cumple,
            SUM(t_cumple) AS t_cumple,
            CONCAT(ROUND( (SUM(t_cumple) * 100) / SUM(t_cumple + t_no_cumple), 1), '%') AS p_cumple,
            CONCAT(ROUND( (SUM(t_no_cumple) * 100) / SUM(t_cumple + t_no_cumple), 1), '%') AS p_no_cumple 
            FROM (
            
                SELECT 
                    GROUP_CONCAT(DISTINCT s.id) as id,
                    o.description as objective,
                    s.description as subobjective,
                    COUNT(DISTINCT ec.id) as t_evaluations,
                    SUM(IF(eir.value = 'NO' OR eir.value = 'pending', 1, 0)) AS t_no_cumple,
                    SUM(IF(eir.value = 'SI', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 0,
                                IF(eir.value IS NULL AND eir.item_id IS NULL,
                                    (SELECT 
                                            COUNT(etr.type_rating_id)
                                        FROM
                                            sau_ct_evaluation_type_rating etr
                                        WHERE
                                            etr.evaluation_id = e.id {$subWhereQualificationTypes}
                                    )
                                , 0)
                            )
                        )
                    ) AS t_cumple
                
                    FROM sau_ct_evaluation_contract ec
                    INNER JOIN sau_ct_evaluations e ON e.id = ec.evaluation_id
                    INNER JOIN sau_ct_objectives o ON o.evaluation_id = e.id
                    INNER JOIN sau_ct_subobjectives s ON s.objective_id = o.id
                    INNER JOIN sau_ct_items i ON i.subobjective_id = s.id
                    LEFT JOIN sau_ct_evaluation_item_rating eir ON eir.item_id = i.id AND eir.evaluation_id = ec.id
                
                    WHERE ec.company_id = ".$this->company. $whereDates . $whereObjectives . $whereSubojectives . $whereQualificationTypes . $whereContract . $whereItems . $whereEvaluations ."
                    GROUP BY objective, subobjective
                ) AS t
            ) AS t"))
            ->first();
    
        return $this->respondHttp200([
            'data' => $evaluations
        ]);
    }

    public function sendNotification($id)
    {
        EvaluationSendNotificationJob::dispatch($this->company, $id);
    }

    public function downloadFile(EvaluationFile $evaluationFile)
    {
        return Storage::disk('s3')->download($evaluationFile->path_donwload());
    }

    public function download(EvaluationContract $evaluationContract)
    {
        try
        {
            EvaluationExportJob::dispatch($this->user, $this->company, [], $evaluationContract->id);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function downloadPdf(EvaluationContract $evaluationContract)
    {
        $evaluations = $this->getDataExportPdf($evaluationContract->id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.evaluationContract', ['evaluations' => $evaluations] );

        $pdf->setPaper('A3', 'landscape');

        return $pdf->download('evaluacion.pdf');
    }

    public function getDataExportPdf($id)
    {
        $evaluationContract = EvaluationContract::findOrFail($id);
        $evaluationContract->evaluators;
        $evaluationContract->contract;
        $evaluationContract->interviewees;
        $evaluation_base = $this->getEvaluation($evaluationContract->evaluation_id);

        $evaluationContract->evaluation = $this->setValuesEvaluation($evaluationContract, $evaluation_base);

        $company = Company::select('logo')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $evaluationContract->logo = $logo;

        return $evaluationContract;
    }

    public function multiselectBar()
    {
        $select = [
            'Evaluaciones' => "evaluation", 
            'Temas' => "objective",
            'Subtemas' => "subobjective",
            'Items' => "item",
            'Proceso a evaluar' => "type_rating",
            'Contratistas' => "contract",

        ];
    
        return $this->multiSelectFormat(collect($select));
    }

    public function reportDinamicBar(Request $request)
    {
        $url = "/legalaspects/evaluations/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $whereDates = '';

        $objectives = !$init ? $this->getValuesForMultiselect($request->evaluationsObjectives) : (isset($filters['evaluationsObjectives']) ? $this->getValuesForMultiselect($filters['evaluationsObjectives']) : []);
        
        $subobjectives = !$init ? $this->getValuesForMultiselect($request->evaluationsSubobjectives) : (isset($filters['evaluationsSubobjectives']) ? $this->getValuesForMultiselect($filters['evaluationsSubobjectives']) : []);
        
        $qualificationTypes = !$init ? $this->getValuesForMultiselect($request->qualificationTypes) : (isset($filters['qualificationTypes']) ? $this->getValuesForMultiselect($filters['qualificationTypes']) : []);
        
        $evaluations = !$init ? $this->getValuesForMultiselect($request->evaluationsEvaluations) : (isset($filters['evaluationsEvaluations']) ? $this->getValuesForMultiselect($filters['evaluationsEvaluations']) : []);
        
        $items = !$init ? $this->getValuesForMultiselect($request->evaluationsItems) : (isset($filters['evaluationsItems']) ? $this->getValuesForMultiselect($filters['evaluationsItems']) : []);
        
        $contract = !$init ? $this->getValuesForMultiselect($request->contracts) : (isset($filters['contracts']) ? $this->getValuesForMultiselect($filters['contracts']) : []);

        $years = $this->getValuesForMultiselect($request->years);
        $months = $this->getValuesForMultiselect($request->months);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $whereObjectives = $this->scopeQueryReport('o', $objectives, $filtersType['evaluationsObjectives']);
        $whereSubojectives = $this->scopeQueryReport('s', $subobjectives, $filtersType['evaluationsSubobjectives']);
        $subWhereQualificationTypes = $this->scopeQueryReport('etr', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $whereQualificationTypes = $this->scopeQueryReport('eir', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $whereEvaluations = $this->scopeQueryReport('e', $evaluations, $filtersType['evaluationsEvaluations']);
        $whereItems = $this->scopeQueryReport('i', $items, $filtersType['evaluationsItems']);
        $whereContract = $this->scopeQueryReport('ec', $contract, $filtersType['contracts'], 'contract_id');
        $whereYear = $this->scopeQueryReport('ec', $years, 'IN', 'evaluation_date', 'YEAR');
        $whereMonth = $this->scopeQueryReport('ec', $months, 'IN', 'evaluation_date', 'month');

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));

                $whereDates = " AND ec.evaluation_date BETWEEN '".$dates[0]."' AND '".$dates[1]."'";
            }
        }

        $report = new InformManagerEvaluationContract($this->company, $whereContract, $whereEvaluations, $whereObjectives, $whereSubojectives, $whereItems, $whereQualificationTypes, $whereDates, $subWhereQualificationTypes, $whereYear, $whereMonth);

        return $this->respondHttp200($report->getInformData());
    }

    public function multiselectYears()
    {
        $years = EvaluationContract::selectRaw(
            'DISTINCT YEAR(sau_ct_evaluation_contract.created_at) AS year'
        )
        ->orderBy('year')
        ->pluck('year', 'year');

      return $this->multiSelectFormat($years);
    }

    public function multiselectMounts()
    {
        $months = EvaluationContract::selectRaw(
            'DISTINCT month(sau_ct_evaluation_contract.created_at) AS month'
        )
        ->orderBy('month')
        ->get();

        $months = $months->map(function($item, $key){
            return [
                "label" => trans("months.$item->month"),
                "month" => $item->month
            ];
        });

        return $this->multiSelectFormat($months->pluck('month', 'label'));
    }
}