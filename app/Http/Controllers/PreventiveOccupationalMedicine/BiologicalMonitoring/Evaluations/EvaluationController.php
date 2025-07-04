<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring\EvaluationRequest;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Evaluation;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Stage;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Criterion;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Item;
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
        /*$this->middleware("permission:contracts_evaluations_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_evaluations_r, {$this->team}");
        $this->middleware("permission:contracts_evaluations_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_evaluations_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:contracts_evaluations_export, {$this->team}", ['only' => 'export']);*/
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
    public function dataAudiometry(Request $request)
    {
        $evaluations = Evaluation::select(
            'sau_bm_evaluations.*')
            ->join('sau_bm_evaluations_stages', 'sau_bm_evaluations_stages.evaluation_id', 'sau_bm_evaluations.id')
            ->join('sau_bm_evaluations_criterion', 'sau_bm_evaluations_criterion.evaluation_stage_id', 'sau_bm_evaluations_stages.id')
            ->where('module_id', 3)
            ->groupBy('sau_bm_evaluations.id')
            ->orderBy('sau_bm_evaluations.id', 'DESC');

        /*$url = "/legalaspects/evaluations";

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
        }*/
            
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
            $evaluation->user_creator_id = $this->user->id;
            $evaluation->module_id = $request->module_id;

            if (!$request->in_edit)
                $evaluation->in_edit = 0;
            else
                $evaluation->in_edit = 1;

            if(!$evaluation->save()){
                return $this->respondHttp500();
            }

            $this->saveObjectives($evaluation, $request->get('stages'));

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

            foreach ($evaluation->stages as $objective)
            {
                $objective->key = Carbon::now()->timestamp + rand(1,10000);

                foreach ($objective->criterion as $subobjective)
                {
                    $subobjective->key = Carbon::now()->timestamp + rand(1,10000);

                    foreach ($subobjective->items as $item)
                    {
                        $item->key = Carbon::now()->timestamp + rand(1,10000);
                        
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

            $this->saveObjectives($evaluation, $request->get('stages'));

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
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
        /*if (count($evaluation->evaluationContracts) > 0)
        {
            return $this->respondWithError('No se puede eliminar la evaluación porque ya existen evaluaciones realizadas asociadas a ella');
        }*/

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
            $objectiveNew = $evaluation->stages()->updateOrCreate(['id'=>$id], $objective);

            $this->saveSubobjectives($objectiveNew, $objective['criterion']);
        }
    }

    private function saveSubobjectives($objective, $subobjectives)
    {
        foreach ($subobjectives as $subobjective)
        {
            $id = isset($subobjective['id']) ? $subobjective['id'] : NULL;
            $subobjectiveNew = $objective->criterion()->updateOrCreate(['id'=>$id], $subobjective);

            $this->saveItems($subobjectiveNew, $subobjective['items']);
        }
    }

    private function saveItems($subobjective, $items)
    {
        foreach ($items as $item)
        {
            $id = isset($item['id']) ? $item['id'] : NULL;
            $itemNew = $subobjective->items()->updateOrCreate(['id'=>$id], $item);
        }
    }

    private function deleteData($data)
    {    
        if (COUNT($data['objectives']) > 0)
            Stage::destroy($data['objectives']);

        if (COUNT($data['subobjectives']) > 0)
            Criterion::destroy($data['subobjectives']);

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
