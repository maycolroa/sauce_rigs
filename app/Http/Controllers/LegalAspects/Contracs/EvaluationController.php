<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\Contracts\EvaluationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LegalAspects\Contracts\Evaluation;
use App\Models\LegalAspects\Contracts\TypeRating;
use App\Models\LegalAspects\Contracts\Interviewee;
use App\Models\LegalAspects\Contracts\Objective;
use App\Models\LegalAspects\Contracts\Subobjective;
use App\Models\LegalAspects\Contracts\Item;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationExportJob;
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
        $evaluations = Evaluation::select(
            'sau_ct_evaluations.*')
            ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
            ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
            ->groupBy('sau_ct_evaluations.id');

        $filters = $request->get('filters');

        if (isset($filters["evaluationsObjectives"]))
          $evaluations->inObjectives($this->getValuesForMultiselect($filters["evaluationsObjectives"]), $filters['filtersType']['evaluationsObjectives']);

        if (isset($filters["evaluationsSubobjectives"]))
          $evaluations->inSubobjectives($this->getValuesForMultiselect($filters["evaluationsSubobjectives"]), $filters['filtersType']['evaluationsSubobjectives']);

        if (isset($filters["dateRange"]) && $filters["dateRange"])
        {
            $dates_request = explode('/', $filters["dateRange"]);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }

            $evaluations->join('sau_ct_evaluation_contract', 'sau_ct_evaluation_contract.evaluation_id', 'sau_ct_evaluations.id');
            $evaluations->betweenDate($dates);
        }
            
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
     * Remove the specified resource from storage.
     *
     * @param  App\LegalAspects\Evaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evaluation $evaluation)
    {
        if (count($evaluation->evaluationContracts) > 0)
        {
            return $this->respondWithError('No se puede eliminar la evaluación porque ya existen evaluaciones realizadas asociadas a ella');
        }

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

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectObjectives(Request $request)
    {
        $objectives = Evaluation::selectRaw(
            "GROUP_CONCAT(sau_ct_objectives.id) as ids,
             sau_ct_objectives.description as name")
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->groupBy('sau_ct_objectives.description')
        ->pluck('ids', 'name');
    
        return $this->multiSelectFormat($objectives);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectSubobjectives(Request $request)
    {
        $subobjectives = Evaluation::selectRaw(
            "GROUP_CONCAT(sau_ct_subobjectives.id) as ids,
             sau_ct_subobjectives.description as name")
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
        ->groupBy('sau_ct_subobjectives.description')
        ->pluck('ids', 'name');
    
        return $this->multiSelectFormat($subobjectives);
    }

    /**
     * Export resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
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

            EvaluationExportJob::dispatch(Auth::user(), Session::get('company_id'), $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
