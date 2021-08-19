<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring\EvaluationPerformRequest;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Evaluation;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerform;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Interviewee;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Item;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\Observation;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationFile;
use App\Models\PreventiveOccupationalMedicine\BiologicalMonitoring\Evaluations\EvaluationPerformItem;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use App\Models\General\Company;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;

class EvaluationPerformController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:contracts_evaluations_perform_evaluation, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_evaluations_view_evaluations_made, {$this->team}", ['except' => ['report', 'getTotales', 'exportReport', 'store', 'getData']] );
        $this->middleware("permission:contracts_evaluations_edit_evaluations_made, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_evaluations_report_view, {$this->team}", ['only' => ['report', 'getTotales']]);
        $this->middleware("permission:contracts_evaluations_report_export, {$this->team}", ['only' => 'exportReport']);*/
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
        $evaluation_perform = EvaluationPerform::select(
                'sau_bm_evaluation_perform.*',
                'sau_users.name as name'
            )
            ->join('sau_users', 'sau_users.id', 'sau_bm_evaluation_perform.evaluator_id')
            ->where('sau_bm_evaluation_perform.evaluation_id', '=', $request->get('modelId'));

        /*$url = "/legalaspects/evaluations/contracts/".$request->get('modelId');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

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
        }*/

        return Vuetable::of($evaluation_perform)
                    ->make();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\EvaluationPerforms\EvaluationPerformRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EvaluationPerformRequest $request)
    {
        Validator::make($request->all(), [
            "evaluation.stages.*.criterion.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg' &&
                        $value->getClientOriginalExtension() != 'pdf')
                        
                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $evaluation_perform = new EvaluationPerform($request->all());
            $evaluation_perform->company_id = $this->company;
            $evaluation_perform->evaluation_date = date('Y-m-d H:i:s');
            $evaluation_perform->evaluator_id = $this->user->id;
            $evaluation_perform->type = $request->type;
            
            if(!$evaluation_perform->save()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluation_perform->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                $evaluation_perform->interviewees()->createMany($request->get('interviewees'));
            }

            $this->saveResults($evaluation_perform, $request->get('evaluation'));

            $this->deleteData($request->get('delete'));

            DB::commit();

            $data = $this->getEvaluationData($evaluation_perform->id);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se realizo la evaluación'
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\EvaluationPerforms\EvaluationPerformRequest $request
     * @param  App\LegalAspects\EvaluationPerform $evaluation_contract
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluationPerformRequest $request, EvaluationPerform $evaluationPerform)
    {
        Validator::make($request->all(), [
            "evaluation.objectives.*.subobjectives.*.items.*.files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && 
                    $value->getClientMimeType() != 'image/png' && 
                    $value->getClientMimeType() != 'image/jpg' &&
                    $value->getClientMimeType() != 'image/jpeg' &&
                    $value->getClientOriginalExtension() != 'pdf')
                    
                    $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();
        
        DB::beginTransaction();

        try
        {
            $evaluationPerform->fill($request->all());

            if(!$evaluationPerform->update()){
                return $this->respondHttp500();
            }

            if ($request->get('evaluators_id') && COUNT($request->get('evaluators_id')) > 0)
            {
                $evaluationPerform->evaluators()->sync($this->getDataFromMultiselect($request->get('evaluators_id')));
            }
            else
            {
                $evaluationPerform->evaluators()->sync([]);
            }

            if ($request->get('interviewees') && COUNT($request->get('interviewees')) > 0)
            {
                foreach ($request->get('interviewees') as $interviewee)
                {    
                    $id = isset($interviewee['id']) ? $interviewee['id'] : NULL;
                    $evaluationPerform->interviewees()->updateOrCreate(['id'=>$id], $interviewee);
                }
            }

            $this->saveResults($evaluationPerform, $request->get('evaluation'));
            
            /*$evaluationPerform->histories()->create([
                'user_id' => $this->user->id
            ]);*/

            $this->deleteData($request->get('delete'));

            DB::commit();

            $data = $this->getEvaluationData($evaluationPerform->id);

            /*if ($evaluationPerform->ready())
                $this->sendNotification($evaluationPerform->id);*/

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $data
        ]);

        return $this->respondHttp200([
            'message' => 'Se actualizo la evaluación'
        ]);
    }

    private function saveResults($evaluationPerform, $evaluation)
    {
        $evaluationPerform->results()->delete();

        ActionPlan::
                user($this->user)
            ->module('biologicalMonitoring/audiometry')
            ->url(url('/administrative/actionplans'));

        foreach ($evaluation['stages'] as $objective)
        {
            foreach ($objective['criterion'] as $subobjective)
            {
                foreach ($subobjective['items'] as $item)
                {
                    $itemModel = Item::find($item['id']);

                    $itemEvaluation = EvaluationPerformItem::firstOrCreate(
                    [
                        'evaluation_id' => $evaluationPerform->id,
                        'item_id' => $item['id'],
                        'value' => $item['value'] ? $item['value'] : NULL
                    ], 
                    [
                        'evaluation_id' => $evaluationPerform->id,
                        'item_id' => $item['id'],
                        'value' => $item['value'] ? $item['value'] : NULL
                    ]);

                    foreach ($item['observations'] as $observation)
                    {
                        $id = isset($observation['id']) ? $observation['id'] : NULL;
                        $evaluationPerform->observations()->updateOrCreate(['id'=>$id], $observation);
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
                                $fileUpload->evaluation_id = $evaluationPerform->id;
                            }

                            if ($create_file)
                            {
                                $file_tmp = $file['file'];
                                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                                $fileUpload->file = $nameFile;
                                $fileUpload->name_file = $file_tmp->getClientOriginalName();
                                $fileUpload->type_file = $file_tmp->extension();
                            }

                            if (!$fileUpload->save())
                                return $this->respondHttp500();
                        }

                        //Borrar archivos reemplazados
                        foreach ($files_names_delete as $keyf => $file)
                        {
                            Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
                        }
                    }

                    $detail_procedence = 'Monitoreo Biologico - Audiometrias. Evaluación: ' . $evaluation['name'] . ' - Etapa: ' .  $objective['description'] . '- Criterio: ' . $subobjective['description'] . ' - Item: ' . $item['description'];

                    ActionPlan::
                        model($itemEvaluation)
                        ->detailProcedence($detail_procedence)
                        ->activities($item['actionPlan'])
                        ->save();
                }
            }
        }

        $state = EvaluationPerformItem::where('evaluation_id', $evaluationPerform->id)->whereNull('value')->get();
        if (COUNT($state) > 0)
            $evaluationPerform->update(['state' => 'En proceso']);
        else
            $evaluationPerform->update(['state' => 'Terminada']);

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
            $evaluationPerform = $this->getEvaluationData($id);

            return $this->respondHttp200([
                'data' => $evaluationPerform
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getEvaluationData($id)
    {
        $evaluationPerform = EvaluationPerform::findOrFail($id);
        
        $evaluators_id = [];

        foreach ($evaluationPerform->evaluators as $key => $value)
        {
            array_push($evaluators_id, $value->multiselect());
        }

        $evaluationPerform->evaluators_id = $evaluators_id;
        $evaluationPerform->multiselect_evaluators_id = $evaluators_id;

        $evaluationPerform->interviewees;
        
        $evaluation_base = $this->getEvaluation($evaluationPerform->evaluation_id);
        $evaluationPerform->evaluation = $this->setValuesEvaluation($evaluationPerform, $evaluation_base);

        $evaluationPerform->delete = [
            'interviewees' => [],
            'observations' => [],
            'files' => []
        ];

        return $evaluationPerform;
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
            $evaluationPerform = new EvaluationPerform;
            $evaluationPerform->evaluation_id = $id;
            $evaluationPerform->evaluators_id = [];
            $evaluationPerform->interviewees = [];
            $evaluationPerform->observation = '';
            $evaluationPerform->type = '';
            $evaluationPerform->evaluation = $this->getEvaluation($id);
        
            $evaluationPerform->delete = [
                'interviewees' => [],
                'observations' => [],
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $evaluationPerform,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    private function getEvaluation($id)
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
                    $item->value = '';
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

    private function setValuesEvaluation($evaluationPerform, $evaluation_base)
    {
        $evaluation = Evaluation::find($evaluationPerform->evaluation_id);

        foreach ($evaluation_base->stages as $objective)
        {
            foreach ($objective->criterion as $subobjective)
            {
                foreach ($subobjective->items as $item)
                {
                    $item->observations = $evaluationPerform->observations()->where('item_id', $item->id)->get();
                    $files = $evaluationPerform->files()->where('item_id', $item->id)->get();

                    $images_pdf = [];
                    $count_pdf = 0;
                    $i = 0;
                    $j = 0;

                    $files->transform(function($file, $indexFile) use (&$images_pdf, &$count_pdf, &$i, &$j) {
                        $file->key = Carbon::now()->timestamp + rand(1,10000);
                        $file->type_file = $file->type_file;
                        $file->name_file = $file->name_file;
                        $file->old_name = $file->file;
                        $file->path = $file->path_image();
                        $images_pdf[$i][$j] = ['file' => $file->path, 'type' => $file->type_file, 'name' => $file->name_file];
                        $j++;

                        if ($j > 3)
                        {
                            $i++;
                            $j = 0;
                        }
                        
                        if ($file->type_file == 'pdf')
                            $count_pdf++;

                        return $file;
                    });

                    $item->files = $files;
                    $item->files_pdf = $images_pdf;
                    $item->count_file_pdf = $count_pdf;

                    $evaluationPerformItem = EvaluationPerformItem::where('evaluation_id', $evaluationPerform->id)->where('item_id',  $item->id)->first();

                    $item->value = $evaluationPerformItem->value;

                    if ($evaluationPerformItem)
                        $item->actionPlan = ActionPlan::model($evaluationPerformItem)->prepareDataComponent();
                    else
                        $item->actionPlan = [
                            "activities" => [],
                            "activitiesRemoved" => []
                        ];
                }
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
                    Storage::disk('s3')->delete($file_delete->path_client(false)."/".$file_delete->file);
                    $file_delete->delete();
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(EvaluationPerform $evaluationPerform)
    {
        DB::beginTransaction();

        try
        { 
            foreach ($evaluationPerform->items as $item)
            {  
                ActionPlan::model($item)->modelDeleteAll();
            }

            foreach ($evaluationPerform->files  as $file)
            {
                $file_delete = EvaluationFile::find($file);

                if ($file_delete)
                {
                    foreach ($file_delete as $file_2) 
                    {
                        Storage::disk('s3')->delete($file_2->path_client(false)."/".$file_2->file);
                        $file_2->delete();
                    }
                }
            }

            if(!$evaluationPerform->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getMessage());
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la evaluación realizada'
        ]);
    }

    public function downloadFile(EvaluationFile $evaluationFile)
    {
        return Storage::disk('s3')->download($evaluationFile->path_donwload());
    }

    /*public function download(EvaluationPerform $evaluationPerform)
    {
        try
        {
            EvaluationExportJob::dispatch($this->user, $this->company, [], $evaluationPerform->id);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }*/

    public function downloadPdf(EvaluationPerform $evaluationPerform)
    {
        $evaluations = $this->getDataExportPdf($evaluationPerform->id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.evaluationPerform', ['evaluations' => $evaluations] );

        $pdf->setPaper('A3', 'landscape');

        return $pdf->download('evaluacion.pdf');
    }

    public function getDataExportPdf($id)
    {
        $evaluationPerform = EvaluationPerform::findOrFail($id);
        $evaluationPerform->evaluators;
        $evaluationPerform->interviewees;
        $evaluation_base = $this->getEvaluation($evaluationPerform->evaluation_id);

        $evaluationPerform->evaluation = $this->setValuesEvaluation($evaluationPerform, $evaluation_base);

        $company = Company::select('logo')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $evaluationPerform->logo = $logo;

        return $evaluationPerform;
    }

    /*public function multiselectBar()
    {
        $select = [
            'Evaluaciones' => "evaluation", 
            'Temas' => "objective",
            'Subtemas' => "subobjective",
            'Items' => "item",
            'Proceso a evaluar' => "type_rating",
            'Contratistas' => "contract",

        ];
    
        return $this->multiSelectFormat(collect($select));
    }

    public function reportDinamicBar(Request $request)
    {
        $url = "/legalaspects/evaluations/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $whereDates = '';

        $objectives = !$init ? $this->getValuesForMultiselect($request->evaluationsObjectives) : (isset($filters['evaluationsObjectives']) ? $this->getValuesForMultiselect($filters['evaluationsObjectives']) : []);
        
        $subobjectives = !$init ? $this->getValuesForMultiselect($request->evaluationsSubobjectives) : (isset($filters['evaluationsSubobjectives']) ? $this->getValuesForMultiselect($filters['evaluationsSubobjectives']) : []);
        
        $qualificationTypes = !$init ? $this->getValuesForMultiselect($request->qualificationTypes) : (isset($filters['qualificationTypes']) ? $this->getValuesForMultiselect($filters['qualificationTypes']) : []);
        
        $evaluations = !$init ? $this->getValuesForMultiselect($request->evaluationsEvaluations) : (isset($filters['evaluationsEvaluations']) ? $this->getValuesForMultiselect($filters['evaluationsEvaluations']) : []);
        
        $items = !$init ? $this->getValuesForMultiselect($request->evaluationsItems) : (isset($filters['evaluationsItems']) ? $this->getValuesForMultiselect($filters['evaluationsItems']) : []);
        
        $contract = !$init ? $this->getValuesForMultiselect($request->contracts) : (isset($filters['contracts']) ? $this->getValuesForMultiselect($filters['contracts']) : []);

        $years = $this->getValuesForMultiselect($request->years);
        $months = $this->getValuesForMultiselect($request->months);
        
        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $whereObjectives = $this->scopeQueryReport('o', $objectives, $filtersType['evaluationsObjectives']);
        $whereSubojectives = $this->scopeQueryReport('s', $subobjectives, $filtersType['evaluationsSubobjectives']);
        $subWhereQualificationTypes = $this->scopeQueryReport('etr', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $whereQualificationTypes = $this->scopeQueryReport('eir', $qualificationTypes, $filtersType['qualificationTypes'], 'type_rating_id');
        $whereEvaluations = $this->scopeQueryReport('e', $evaluations, $filtersType['evaluationsEvaluations']);
        $whereItems = $this->scopeQueryReport('i', $items, $filtersType['evaluationsItems']);
        $whereContract = $this->scopeQueryReport('ec', $contract, $filtersType['contracts'], 'contract_id');
        $whereYear = $this->scopeQueryReport('ec', $years, 'IN', 'evaluation_date', 'YEAR');
        $whereMonth = $this->scopeQueryReport('ec', $months, 'IN', 'evaluation_date', 'month');

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

        if (isset($datesF) && $datesF)
        {
            $dates_request = explode('/', $datesF);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));

                $whereDates = " AND ec.evaluation_date BETWEEN '".$dates[0]."' AND '".$dates[1]."'";
            }
        }

        $report = new InformManagerEvaluationPerform($this->company, $whereContract, $whereEvaluations, $whereObjectives, $whereSubojectives, $whereItems, $whereQualificationTypes, $whereDates, $subWhereQualificationTypes, $whereYear, $whereMonth);

        return $this->respondHttp200($report->getInformData());
    }

    public function multiselectYears()
    {
        $years = EvaluationPerform::selectRaw(
            'DISTINCT YEAR(sau_ct_evaluation_contract.created_at) AS year'
        )
        ->orderBy('year')
        ->pluck('year', 'year');

      return $this->multiSelectFormat($years);
    }

    public function multiselectMounts()
    {
        $months = EvaluationPerform::selectRaw(
            'DISTINCT month(sau_ct_evaluation_contract.created_at) AS month'
        )
        ->orderBy('month')
        ->get();

        $months = $months->map(function($item, $key){
            return [
                "label" => trans("months.$item->month"),
                "month" => $item->month
            ];
        });

        return $this->multiSelectFormat($months->pluck('month', 'label'));
    }*/
}