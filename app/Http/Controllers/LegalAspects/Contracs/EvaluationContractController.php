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
use App\Models\LegalAspects\Contracts\Interviewee;
use App\Models\LegalAspects\Contracts\Item;
use App\Models\LegalAspects\Contracts\Observation;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use App\Models\LegalAspects\Contracts\EvaluationContractItem;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationContractReportExportJob;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationExportJob;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationSendNotificationJob;
use Carbon\Carbon;
use DB;
use Validator;

class EvaluationContractController extends Controller
{
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

        $filters = $request->get('filters');

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
                    if (!is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');
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

            /*if ($evaluation_contract->ready())
                $this->sendNotification($evaluation_contract->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

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
                    if (!is_string($value) && $value->getClientMimeType() != 'application/pdf')
                        $fail('Archivo debe ser un pdf');
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

            /*if ($evaluationContract->ready())
                $this->sendNotification($evaluationContract->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

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
                                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 'public');
                                $fileUpload->file = $nameFile;
                            }

                            if (!$fileUpload->save())
                                return $this->respondHttp500();
                        }

                        //Borrar archivos reemplazados
                        foreach ($files_names_delete as $keyf => $file)
                        {
                            Storage::disk('public')->delete($fileUpload->path_client(false)."/".$file);
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

                    ActionPlan::
                        model($itemEvaluation)
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

            return $this->respondHttp200([
                'data' => $evaluationContract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
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

        foreach ($evaluation->ratingsTypes()->get() as $key => $value)
        {
            $report[$value->id] = [
                'total' => 0,
                'total_c' => 0,
                'percentage' =>0
            ];
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
                    
                    $files->transform(function($file, $indexFile) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->old_name = $file->file;

                        return $file;
                    });

                    $item->files = $files;

                    $values = $evaluationContract->results()->where('item_id', $item->id)->pluck('value', 'type_rating_id');
                    $clone = $item->ratings;

                    foreach ($clone as $index => $rating)
                    {
                        $clone[$index]['value'] = isset($values[$index]) ? $values[$index] : NULL;
                        $clone_report[$index]['total'] += 1;

                        if ($rating['apply'] == 'SI')
                        {
                            if (!$clone[$index]['value'])
                                $clone_report[$index]['total_c'] += 1;
                            else 
                            {
                                if ($clone[$index]['value'] == 'SI' || $clone[$index]['value'] == 'N/A')
                                    $clone_report[$index]['total_c'] += 1;
                            }
                        }
                        else 
                        {
                            $clone_report[$index]['total_c'] += 1;
                        }

                        $clone_report[$index]['percentage'] = round(($clone_report[$index]['total_c'] / $clone_report[$index]['total']) * 100, 1);
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
            }
        }

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
                    Storage::disk('public')->delete($file_delete->path_client(false)."/".$file_delete->file);
                    $file_delete->delete();
                }
            }
        }
    }

    public function report(Request $request)
    {
        $whereObjectives = '';
        $whereSubojectives = '';
        $whereDates = '';
        $whereQualificationTypes = '';
        $subWhereQualificationTypes = '';
        
        $filters = $request->get('filters');

        if (isset($filters["evaluationsObjectives"]))
            $whereObjectives = $this->scopeQueryReport('o', $this->getValuesForMultiselect($filters["evaluationsObjectives"]), $filters['filtersType']['evaluationsObjectives']);

        if (isset($filters["evaluationsSubobjectives"]))
            $whereSubojectives = $this->scopeQueryReport('s', $this->getValuesForMultiselect($filters["evaluationsSubobjectives"]), $filters['filtersType']['evaluationsSubobjectives']);

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
                    SUM(IF(eir.value = 'SI' OR eir.value = 'N/A' , 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
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
                
                    WHERE ec.company_id = ".$this->company. $whereDates . $whereObjectives . $whereSubojectives . $whereQualificationTypes ."
                    GROUP BY objective, subobjective
                ) AS t
            ) AS t"));
    
        return Vuetable::of($evaluations)
            ->make();
    }

    private function scopeQueryReport($table, $data, $typeSearch, $primary = 'id')
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
                $query = " AND $table.$primary IN ($ids)";

            else if ($typeSearch == 'NOT IN')
                $query = " AND $table.$primary NOT IN ($ids)";
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
            $objectives = $this->getValuesForMultiselect($request->evaluationsObjectives);
            $subobjectives = $this->getValuesForMultiselect($request->evaluationsSubobjectives);
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
                'filtersType' => $filtersType
            ];

            EvaluationContractReportExportJob::dispatch($this->user, $this->company, $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function getTotales(Request $request)
    {
        $whereDates = '';

        $objectives = $this->getValuesForMultiselect($request->evaluationsObjectives);
        $subobjectives = $this->getValuesForMultiselect($request->evaluationsSubobjectives);
        $qualificationTypes = $this->getValuesForMultiselect($request->qualificationTypes);
        $filtersType = $request->filtersType;

        $whereObjectives = $this->scopeQueryReport('o', $objectives, $filtersType['evaluationsObjectives']);
        $whereSubojectives = $this->scopeQueryReport('s', $subobjectives, $filtersType['evaluationsSubobjectives']);
        $whereQualificationTypes = $this->scopeQueryReport('eir', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $subWhereQualificationTypes = $this->scopeQueryReport('etr', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');

        if (isset($request->dateRange) && $request->dateRange)
        {
            $dates_request = explode('/', $request->dateRange);
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
                    SUM(IF(eir.value = 'SI' OR eir.value = 'N/A', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
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
                
                    WHERE ec.company_id = ".$this->company. $whereDates . $whereObjectives . $whereSubojectives . $whereQualificationTypes ."
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
        return Storage::disk('public')->download($evaluationFile->path_donwload());
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
}