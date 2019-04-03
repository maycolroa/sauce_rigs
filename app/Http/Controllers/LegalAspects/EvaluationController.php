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
    private $typesRating = [];
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
        $evaluations = Evaluation::select('*');

        return Vuetable::of($evaluations)
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

            if ($request->get('types_rating') && COUNT($request->get('types_rating')) > 0)
            {
                foreach ($request->get('types_rating') as $rating)
                {   
                    array_push($this->typesRating, $rating['type_rating_id']);
                }

                $evaluation->ratingsTypes()->sync($this->typesRating);
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

            $types_rating = TypeRating::get();
            $types = [];

            foreach ($types_rating as $key => $value)
            {
                $types[$value->id] = [
                    'item_id' => '',
                    'type_rating_id' => $value->id,
                    'apply' => 'NO',
                    'value' => ''
                ];
            }

            foreach ($evaluation->objectives as $objective)
            {
                $objective->key = Carbon::now()->timestamp + rand(1,10000);

                foreach ($objective->subobjectives as $subobjective)
                {
                    $subobjective->key = Carbon::now()->timestamp + rand(1,10000);

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
                            }
                        }

                        $item->ratings = $clone_types;
                    }
                }
            }

            $evaluation->delete = [
                'objectives' => [],
                'subobjectives' => [],
                'items' => []
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

            if ($request->get('types_rating') && COUNT($request->get('types_rating')) > 0)
            {
                foreach ($request->get('types_rating') as $rating)
                {   
                    array_push($this->typesRating, $rating['type_rating_id']);
                }

                $evaluation->ratingsTypes()->sync($this->typesRating);
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
            if (in_array($rating['type_rating_id'], $this->typesRating))
                $ids[$rating['type_rating_id']] = [
                    'apply' => $rating['apply']
                ];
        }

        $item->ratingsTypes()->sync($ids);
    }

    private function deleteData($data)
    {    
        if (COUNT($data['objectives']) > 0)
            Objective::destroy($data['objectives']);

        if (COUNT($data['subobjectives']) > 0)
            Subobjective::destroy($data['subobjectives']);

        if (COUNT($data['items']) > 0)
            Item::destroy($data['items']);
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
