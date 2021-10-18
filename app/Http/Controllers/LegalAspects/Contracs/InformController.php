<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Http\Requests\LegalAspects\Contracts\InformRequest;
use App\Models\LegalAspects\Contracts\Inform;
use App\Models\LegalAspects\Contracts\Theme;
use App\Jobs\LegalAspects\Contracts\Evaluations\EvaluationExportJob;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use DB;

class InformController extends Controller
{
    use Filtertrait;
    
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_informs_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_informs_r, {$this->team}");
        $this->middleware("permission:contracts_informs_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_informs_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:contracts_informs_export, {$this->team}", ['only' => 'export']);
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
        $informs = Inform::select(
            'sau_ct_informs.*')
            ->join('sau_ct_informs_themes', 'sau_ct_informs_themes.inform_id', 'sau_ct_informs.id')
            ->groupBy('sau_ct_informs.id');

        /*$url = "/legalaspects/informs";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (isset($filters["evaluationsObjectives"]))
          $evaluations->inObjectives($this->getValuesForMultiselect($filters["evaluationsObjectives"]), $filters['filtersType']['evaluationsObjectives']);

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
            
        return Vuetable::of($informs)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\Evaluations\InformRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(InformRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $inform = new Inform($request->all());
            $inform->company_id = $this->company;
            $inform->user_creator_id  = $this->user->id;

            if(!$inform->save()){
                return $this->respondHttp500();
            }

            $this->saveThemes($inform, $request->get('themes'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el informe'
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
            $inform = Inform::findOrFail($id);

            foreach ($inform->themes as $theme)
            {
                $theme->key = Carbon::now()->timestamp + rand(1,10000);
            }

            $inform->delete = [
                'themes' => []
            ];

            return $this->respondHttp200([
                'data' => $inform,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\TypesRating\InformRequest $request
     * @param  App\LegalAspects\Evaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(InformRequest $request, Inform $inform)
    {
        DB::beginTransaction();

        try
        {
            $inform->fill($request->all());

            if(!$inform->update()){
                return $this->respondHttp500();
            }

            $this->saveThemes($inform, $request->get('themes'));

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //\Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el informe'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\LegalAspects\Inform $inform
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inform $inform)
    {
        if (count($inform->informContracts) > 0)
        {
            return $this->respondWithError('No se puede eliminar el informe porque ya existen informes realizados asociados a el');
        }

        if(!$inform->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el informe'
        ]);
    }

    private function saveThemes($inform, $themes)
    {
        foreach ($themes as $theme)
        {
            $id = isset($theme['id']) ? $theme['id'] : NULL;
            $themeNew = $inform->themes()->updateOrCreate(['id'=>$id], $theme);;
        }
    }

    private function deleteData($data)
    {    
        if (COUNT($data['themes']) > 0)
            Objective::destroy($data['themes']);

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
        ->pluck('ids', 'name');
    
        return $this->multiSelectFormat($objectives);
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
