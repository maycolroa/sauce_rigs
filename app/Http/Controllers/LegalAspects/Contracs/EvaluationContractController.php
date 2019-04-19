<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\Contracts\EvaluationContractRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LegalAspects\Contracts\Evaluation;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\Interviewee;
use App\Models\LegalAspects\Contracts\Item;
use App\Models\LegalAspects\Contracts\Observation;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationContractReportExportJob;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationSendNotificationJob;
use Carbon\Carbon;
use Session;
use DB;

class EvaluationContractController extends Controller
{
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
            $evaluation_contracts->where('sau_user_information_contract_lessee.user_id', '=', Auth::user()->id);
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
        DB::beginTransaction();

        try
        {
            $evaluation_contract = new EvaluationContract($request->all());
            $evaluation_contract->company_id = Session::get('company_id');
            $evaluation_contract->evaluation_date = date('Y-m-d H:i:s');
            $evaluation_contract->evaluator_id = Auth::user()->id;

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

            $this->sendNotification($evaluation_contract->id);

        } catch (\Exception $e) {
            DB::rollback();
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
                'user_id' => Auth::user()->id
            ]);

            $this->deleteData($request->get('delete'));

            DB::commit();

            $this->sendNotification($evaluationContract->id);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la evaluación'
        ]);
    }

    private function saveResults($evaluationContract, $evaluation)
    {
        $evaluationContract->results()->delete();

        foreach ($evaluation['objectives'] as $objective)
        {
            foreach ($objective['subobjectives'] as $subobjective)
            {
                foreach ($subobjective['items'] as $item)
                {
                    $itemModel = Item::find($item['id']);
                    
                    foreach ($item['ratings'] as $rating)
                    {
                        $evaluationContract->results()->updateOrCreate(['item_id'=>$itemModel->id, 'type_rating_id'=>$rating['type_rating_id']], $rating);
                    }

                    foreach ($item['observations'] as $observation)
                    {
                        $id = isset($observation['id']) ? $observation['id'] : NULL;
                        $evaluationContract->observations()->updateOrCreate(['id'=>$id], $observation);
                    }
                }
            }
        }
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
                'observations' => []
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
            $evaluationContract->evaluation = $this->getEvaluation($id);
        
            $evaluationContract->delete = [
                'interviewees' => [],
                'observations' => []
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
                                if ($clone[$index]['value'] == 'SI')
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
    }

    public function report(Request $request)
    {
        $whereObjectives = '';
        $whereSubojectives = '';
        $whereDates = '';
        
        $filters = $request->get('filters');

        if (isset($filters["evaluationsObjectives"]))
            $whereObjectives = $this->scopeQueryReport('o', $this->getValuesForMultiselect($filters["evaluationsObjectives"]), $filters['filtersType']['evaluationsObjectives']);

        if (isset($filters["evaluationsSubobjectives"]))
            $whereSubojectives = $this->scopeQueryReport('s', $this->getValuesForMultiselect($filters["evaluationsSubobjectives"]), $filters['filtersType']['evaluationsSubobjectives']);

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
                    SUM(IF(eir.value = 'NO', 1, 0)) AS t_no_cumple,
                    SUM(IF(eir.value = 'SI', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
                                IF(eir.value IS NULL AND eir.item_id IS NULL,
                                    (SELECT 
                                            COUNT(etr.type_rating_id)
                                        FROM
                                            sau_ct_evaluation_type_rating etr
                                        WHERE
                                            etr.evaluation_id = e.id
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
                
                    WHERE ec.company_id = ".Session::get('company_id'). $whereDates . $whereObjectives . $whereSubojectives ."
                    GROUP BY objective, subobjective
                ) AS t
            ) AS t"));
    
        return Vuetable::of($evaluations)
            ->make();
    }

    private function scopeQueryReport($table, $data, $typeSearch)
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
                $query = " AND $table.id IN ($ids)";

            else if ($typeSearch == 'NOT IN')
                $query = " AND $table.id NOT IN ($ids)";
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
                'dates' => $dates,
                'filtersType' => $filtersType
            ];

            EvaluationContractReportExportJob::dispatch(Auth::user(), Session::get('company_id'), $filters);
        
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
        $filtersType = $request->filtersType;

        $whereObjectives = $this->scopeQueryReport('o', $objectives, $filtersType['evaluationsObjectives']);
        $whereSubojectives = $this->scopeQueryReport('s', $subobjectives, $filtersType['evaluationsSubobjectives']);

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
                    SUM(IF(eir.value = 'NO', 1, 0)) AS t_no_cumple,
                    SUM(IF(eir.value = 'SI', 1,
                            IF(eir.value IS NULL AND eir.item_id IS NOT NULL, 1,
                                IF(eir.value IS NULL AND eir.item_id IS NULL,
                                    (SELECT 
                                            COUNT(etr.type_rating_id)
                                        FROM
                                            sau_ct_evaluation_type_rating etr
                                        WHERE
                                            etr.evaluation_id = e.id
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
                
                    WHERE ec.company_id = ".Session::get('company_id'). $whereDates . $whereObjectives . $whereSubojectives ."
                    GROUP BY objective, subobjective
                ) AS t
            ) AS t"))
            ->first();
    
        return $this->respondHttp200([
            'data' => $evaluations
        ]);
    }

    private function sendNotification($id)
    {
        EvaluationSendNotificationJob::dispatch(Session::get('company_id'), $id);
    }
}