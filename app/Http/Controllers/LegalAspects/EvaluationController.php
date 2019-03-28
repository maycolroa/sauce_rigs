<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\Evaluations\EvaluationRequest;
use App\Http\Requests\LegalAspects\Evaluations\EvaluateRequest;
use Illuminate\Support\Facades\Auth;
use App\LegalAspects\Evaluation;
use App\LegalAspects\TypeRating;
use App\LegalAspects\Interviewee;
use App\LegalAspects\Objective;
use App\LegalAspects\Subobjective;
use App\LegalAspects\Item;
use App\LegalAspects\Observation;
use Carbon\Carbon;
use Session;
use DB;

class EvaluationController extends Controller
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
        $evaluations = Evaluation::selectRaw(
            'sau_ct_evaluations.*,
             DATE(sau_ct_evaluations.created_at) as date,
             sau_ct_information_contract_lessee.nit as nit,
             sau_users.name as creator'
        )->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluations.information_contract_lessee_id')
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluations.creator_user_id');

        return Vuetable::of($evaluations)
                    ->addColumn('legalaspects-evaluations-edit', function ($evaluation) {
                        if ($evaluation->evaluation_date)
                            return false;

                        return true;
                    })
                    ->addColumn('legalaspects-evaluations-evaluate', function ($evaluation) {
                        if ($evaluation->evaluation_date || !$this->verifyPermissionEvaluate($evaluation->id))
                            return false;

                        return true;
                    })
                    ->addColumn('control_delete', function ($evaluation) {
                        if ($evaluation->evaluation_date)
                            return false;

                        return true;
                    })
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\Evaluations\EvaluationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EvaluationRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $evaluation = new Evaluation($request->all());
            $evaluation->company_id = Session::get('company_id');
            $evaluation->creator_user_id = Auth::user()->id;

            if(!$evaluation->save()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluation->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                $evaluation->interviewees()->createMany($request->get('interviewees'));
            }

            $this->saveObjectives($evaluation, $request->get('objectives'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la evaluación'
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
            $evaluation = Evaluation::findOrFail($id);
            $evaluation->multiselect_information_contract_lessee_id = $evaluation->contract->multiselect();
            $evaluators_id = [];

            foreach ($evaluation->evaluators as $key => $value)
            {
                array_push($evaluators_id, $value->multiselect());
            }

            $evaluation->evaluators_id = $evaluators_id;
            $evaluation->multiselect_evaluators_id = $evaluators_id;
            $evaluation->interviewees;

            $types_rating = TypeRating::get();
            $types = [];
            $report = [];

            foreach ($types_rating as $key => $value)
            {
                $types[$value->id] = [
                    'item_id' => '',
                    'type_rating_id' => $value->id,
                    'apply' => 'NO',
                    'value' => ''
                ];

                $report[$value->id] = [
                    'total' => 0,
                    'total_c' => 0,
                    'percentage' =>0
                ];
            }

            foreach ($evaluation->objectives as $objective)
            {
                $objective->key = Carbon::now()->timestamp + rand(1,10000);

                foreach ($objective->subobjectives as $subobjective)
                {
                    $subobjective->key = Carbon::now()->timestamp + rand(1,10000);
                    $clone_report = $report;

                    foreach ($subobjective->items as $item)
                    {
                        $item->key = Carbon::now()->timestamp + rand(1,10000);
                        
                        $clone_types = $types;

                        foreach ($item->ratingsTypes as $rating)
                        {
                            if (isset($clone_types[$rating->id]))
                            {
                                $clone_types[$rating->id]['item_id'] = $item->id;
                                $clone_types[$rating->id]['apply'] = $rating->pivot->apply;
                                $clone_types[$rating->id]['value'] = $rating->pivot->value;

                                if ($evaluation->evaluation_date)
                                {
                                    if ($rating->pivot->apply == 'SI')
                                    {
                                        $clone_report[$rating->id]['total'] += 1;

                                        if ($rating->pivot->value == 'SI')
                                            $clone_report[$rating->id]['total_c'] += 1;

                                        $clone_report[$rating->id]['percentage'] = round(($clone_report[$rating->id]['total_c'] / $clone_report[$rating->id]['total']) * 100, 1);
                                    }
                                }
                            }
                        }

                        $item->ratings = $clone_types;
                        $item->observations;
                    }
                    
                    if ($evaluation->evaluation_date)
                        $subobjective->report = $clone_report;
                }
            }

            $evaluation->delete = [
                'interviewees' => [],
                'objectives' => [],
                'subobjectives' => [],
                'items' => [],
                'observations' => []
            ];

            return $this->respondHttp200([
                'data' => $evaluation,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\TypesRating\EvaluationRequest $request
     * @param  App\LegalAspects\Evaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluationRequest $request, Evaluation $evaluation)
    {
        DB::beginTransaction();

        try
        {
            $evaluation->fill($request->all());

            if(!$evaluation->update()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluation->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }
            else
            {
                $evaluation->evaluators()->sync([]);
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                foreach ($request->get('interviewees') as $interviewee)
                {    
                    $id = isset($interviewee['id']) ? $interviewee['id'] : NULL;
                    $evaluation->interviewees()->updateOrCreate(['id'=>$id], $interviewee);
                }
            }

            $this->saveObjectives($evaluation, $request->get('objectives'));

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

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\TypesRating\EvaluateRequest $request
     * @param  App\LegalAspects\Evaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function evaluate(EvaluateRequest $request, Evaluation $evaluation)
    {
        if (!$this->verifyPermissionEvaluate($evaluation->id))
            return $this->respondWithError('No tiene permitido realizar esta evaluación');
        
        DB::beginTransaction();

        try
        {
            $evaluation->evaluation_date = date('Y-m-d');

            if(!$evaluation->update()){
                return $this->respondHttp500();
            }

            foreach ($request->get('objectives') as $objective)
            {
                foreach ($objective['subobjectives'] as $subobjective)
                {
                    foreach ($subobjective['items'] as $item)
                    {
                        $itemModel = Item::find($item['id']);
                        
                        foreach ($item['ratings'] as $rating)
                        {
                            $value = NULL;

                            if ($rating['apply'] == 'SI')
                                $value = $rating['value'];

                            $itemModel->ratingsTypes()->updateExistingPivot($rating['type_rating_id'], ['value'=>$value]);
                        }

                        foreach ($item['observations'] as $observation)
                        {
                            $id = isset($observation['id']) ? $observation['id'] : NULL;
                            $itemModel->observations()->updateOrCreate(['id'=>$id], $observation);
                        }
                    }
                }
            }

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
     * Remove the specified resource from storage.
     *
     * @param  App\LegalAspects\TypeRating $typeRating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evaluation $evaluation)
    {
        if(!$evaluation->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la evaluación'
        ]);
    }

    private function saveObjectives($evaluation, $objectives)
    {
        foreach ($objectives as $objective)
        {
            $id = isset($objective['id']) ? $objective['id'] : NULL;
            $objectiveNew = $evaluation->objectives()->updateOrCreate(['id'=>$id], $objective);

            $this->saveSubobjectives($objectiveNew, $objective['subobjectives']);
        }
    }

    private function saveSubobjectives($objective, $subobjectives)
    {
        foreach ($subobjectives as $subobjective)
        {
            $id = isset($subobjective['id']) ? $subobjective['id'] : NULL;
            $subobjectiveNew = $objective->subobjectives()->updateOrCreate(['id'=>$id], $subobjective);

            $this->saveItems($subobjectiveNew, $subobjective['items']);
        }
    }

    private function saveItems($subobjective, $items)
    {
        foreach ($items as $item)
        {
            $id = isset($item['id']) ? $item['id'] : NULL;
            $itemNew = $subobjective->items()->updateOrCreate(['id'=>$id], $item);

            $this->saveRating($itemNew, array_filter($item['ratings']));
        }
    }

    private function saveRating($item, $ratings)
    {
        $ids = [];

        foreach ($ratings as $rating)
        {   
            $ids[$rating['type_rating_id']] = [
                'value' => $rating['value'] ? $rating['value'] : NULL,
                'apply' => $rating['apply']
            ];
        }

        $item->ratingsTypes()->sync($ids);
    }

    private function deleteData($data)
    {
        if (COUNT($data['interviewees']) > 0)
            Interviewee::destroy($data['interviewees']);

        if (COUNT($data['objectives']) > 0)
            Objective::destroy($data['objectives']);

        if (COUNT($data['subobjectives']) > 0)
            Subobjective::destroy($data['subobjectives']);

        if (COUNT($data['items']) > 0)
            Item::destroy($data['items']);

        if (COUNT($data['observations']) > 0)
            Observation::destroy($data['observations']);
    }

    public function verifyPermissionEvaluate($id)
    {
        $evaluation = Evaluation::findOrFail($id);

        if ($evaluation)
        {
            $evaluators_id = [];
            array_push($evaluators_id, $evaluation->creator_user_id);

            foreach ($evaluation->evaluators as $key => $value)
            {
                array_push($evaluators_id, $value->id);
            }

            if (in_array(Auth::user()->id, $evaluators_id))
                return true;
        }

        return false;
    }
}
