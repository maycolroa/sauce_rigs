<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\Contracts\EvaluationRequest;
use App\Models\LegalAspects\Contracts\Evaluation;
use App\Models\LegalAspects\Contracts\TypeRating;
use App\Models\LegalAspects\Contracts\Interviewee;
use App\Models\LegalAspects\Contracts\Objective;
use App\Models\LegalAspects\Contracts\Subobjective;
use App\Models\LegalAspects\Contracts\Item;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationExportJob;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use DB;

class EvaluationController extends Controller
{
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_evaluations_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_evaluations_r, {$this->team}");
        $this->middleware("permission:contracts_evaluations_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_evaluations_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:contracts_evaluations_export, {$this->team}", ['only' => 'export']);
    }

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
            ->groupBy('sau_ct_evaluations.id')
            ->orderBy('sau_ct_evaluations.id', 'DESC');

        $url = "/legalaspects/evaluations";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

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
            $evaluation->company_id = $this->company;
            $evaluation->creator_user_id = $this->user->id;
            $evaluation->in_edit = 0;

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

            $this->saveLogActivitySystem('Contratistas - Evaluaciones', 'Se creo el formato de evaluacion '.$evaluation->name.' - '.$evaluation->type);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
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
            $evaluation->in_edit = false;
            $evaluation->user_edit = null;

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

            $this->saveLogActivitySystem('Contratistas - Evaluaciones', 'Se edito el formato de evaluacion '.$evaluation->name.' - '.$evaluation->type);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //\Log::info($e->getMessage());
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

        $this->saveLogDelete('Contratistas - Evaluaciones', 'Se elimino el formato de evaluación '.$evaluation->name);

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
        {
            foreach ($data['items'] as $value) 
            {
                Item::find($value)->delete();
            }
        }  
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectEvaluations(Request $request)
    {
        $evaluations = Evaluation::selectRaw(
            "sau_ct_evaluations.id as id,
             sau_ct_evaluations.name as name")
        ->orderBy('name')
        ->pluck('id', 'name');
    
        return $this->multiSelectFormat($evaluations);
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
        ->orderBy('sau_ct_objectives.description')
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
        ->orderBy('sau_ct_subobjectives.description')
        ->pluck('ids', 'name');
    
        return $this->multiSelectFormat($subobjectives);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectItems(Request $request)
    {
        $items = Evaluation::selectRaw(
            "GROUP_CONCAT(sau_ct_items.id) as ids,
             sau_ct_items.description as name")
        ->join('sau_ct_objectives', 'sau_ct_objectives.evaluation_id', 'sau_ct_evaluations.id')
        ->join('sau_ct_subobjectives', 'sau_ct_subobjectives.objective_id', 'sau_ct_objectives.id')
        ->join('sau_ct_items', 'sau_ct_items.subobjective_id', 'sau_ct_subobjectives.id')
        ->groupBy('sau_ct_items.description')
        ->orderBy('sau_ct_items.description')
        ->pluck('ids', 'name');
    
        return $this->multiSelectFormat($items);
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

            EvaluationExportJob::dispatch($this->user, $this->company, $filters);
        
            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function inEdit(Request $request)
    {
        try
        {
            $evaluation = Evaluation::findOrFail($request->id);
            $evaluation->in_edit = true;
            $evaluation->user_edit = $this->user->name. ' - ' .$this->user->email;
            $evaluation->time_edit = Carbon::now();

            if(!$evaluation->save()){
                return $this->respondHttp500();
            }

            return $this->respondHttp200();

        } catch (\Exception $e) {
            return $this->respondHttp500();
        }
    }
}
