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
    /*public function index()
    {
        return view('application');
    }*/
    
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

            $evaluation = $request->get('evaluation');

            foreach ($evaluation['objectives'] as $objective)
            {
                foreach ($objective['subobjectives'] as $subobjective)
                {
                    foreach ($subobjective['items'] as $item)
                    {
                        $itemModel = Item::find($item['id']);
                        
                        /*foreach ($item['ratings'] as $rating)
                        {
                            $value = NULL;

                            if ($rating['apply'] == 'SI')
                                $value = $rating['value'];

                            $itemModel->ratingsTypes()->updateExistingPivot($rating['type_rating_id'], ['value'=>$value]);
                        }*/

                        foreach ($item['observations'] as $observation)
                        {
                            $id = isset($observation['id']) ? $observation['id'] : NULL;
                            $evaluation_contract->observations()->updateOrCreate(['id'=>$id], $observation);
                        }
                    }
                }
            }

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //return $this->respondHttp500();
            return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se realizo la evaluación'
        ]);
        
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
                        $item_types[$rating->id]['type_rating_id'] = $item->id;
                        $item_types[$rating->id]['item_id'] = $item->id;
                        $item_types[$rating->id]['apply'] = $rating->pivot->apply;
                        $item_types[$rating->id]['value'] = '';
                    }

                    $item->ratings = $item_types;
                    $item->observations = [];
                }
            }
        }

        $evaluation->delete = [
            'interviewees' => [],
            'observations' => []
        ];

        return $evaluation;
    }

    private function deleteData($data)
    {
        if (COUNT($data['interviewees']) > 0)
            Interviewee::destroy($data['interviewees']);

        if (COUNT($data['observations']) > 0)
            Observation::destroy($data['observations']);
    }
}