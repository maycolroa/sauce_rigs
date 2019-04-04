<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\EvaluationContracts\EvaluationContractRequest;
use Illuminate\Support\Facades\Auth;
use App\LegalAspects\Evaluation;
use App\LegalAspects\EvaluationContract;
use App\LegalAspects\TypeRating;
use App\LegalAspects\Interviewee;
use App\LegalAspects\Objective;
use App\LegalAspects\Subobjective;
use App\LegalAspects\Item;
use App\LegalAspects\Observation;
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
                'sau_ct_information_contract_lessee.nit as nit'
            )
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $evaluation_contracts->where('sau_ct_evaluation_contract.evaluation_id', '=', $request->get('modelId'));

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
            $evaluation_contract->evaluation_date = date('Y-m-d');

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
                        $clone[$index]['value'] = $values[$index];

                        if ($rating['apply'] == 'SI')
                        {
                            $clone_report[$index]['total'] += 1;

                            if ($clone[$index]['value'] == 'SI')
                                $clone_report[$index]['total_c'] += 1;

                            $clone_report[$index]['percentage'] = round(($clone_report[$index]['total_c'] / $clone_report[$index]['total']) * 100, 1);
                        }
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
}