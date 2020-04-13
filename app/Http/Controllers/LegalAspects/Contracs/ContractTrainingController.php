<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\Training;
use App\Http\Requests\LegalAspects\Contracts\TrainingRequest;
use App\Models\LegalAspects\Contracts\TrainingQuestions;
use App\Models\LegalAspects\Contracts\TrainingTypeQuestion;
use Carbon\Carbon;
use Validator;
use DB;

class ContractTrainingController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_activities_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_activities_r, {$this->team}", ['except' =>['multiselect', 'download']]);
        $this->middleware("permission:contracts_activities_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_activities_d, {$this->team}", ['only' => 'destroy']);
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
        $trainings = Training::select('*');

        return Vuetable::of($trainings)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrainingRequest $request)
    {
        Validator::make($request->all(), [
            "file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'pdf')
                            $fail('Archivo debe ser un pdf');
                    }
                }

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $training = new Training($request->all());
            $training->company_id = $this->company;
            $training->creator_user = $this->user->id;
            $training->modifier_user = $this->user->id;

            if (!$training->save())
                return $this->respondHttp500();

            $activities = $this->getDataFromMultiselect($request->activity_id);
            $training->activities()->sync($activities);

            $this->saveFile($training, $request);

            $this->saveQuestions($training, $request->get('questions'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la capacitación'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $training = Training::findOrFail($id);
            $training->old_file = $training->file;

            foreach ($training->questions as $question)
            {
                $question->key = Carbon::now()->timestamp + rand(1,10000);
                $question->multiselect_type_question_id = $question->type->multiselect();

                if ($question->type_question_id == 1 || $question->type_question_id == 3)
                {
                    $question->options = implode("\n", $question->answer_options->get('options'));
                }

                $question->answers = implode("\n", $question->answer_options->get('answers'));
            }

            $training->delete = [
                'questions' => []
            ];

            $activity_id = [];

            foreach ($training->activities as $key => $value)
            {                
                array_push($activity_id, $value->multiselect());
            }

            $training->multiselect_activity = $activity_id;
            $training->activity_id = $activity_id;

            return $this->respondHttp200([
                'data' => $training
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityContractRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(TrainingRequest $request, Training $trainingContract)
    {
        DB::beginTransaction();

        try
        {
            $trainingContract->fill($request->except('file'));
            $trainingContract->modifier_user = $this->user->id;

            if (!$trainingContract->update())
                return $this->respondHttp500();

            $activitiesTraining = $this->getDataFromMultiselect($request->activity_id);
            $trainingContract->activities()->sync($activitiesTraining);

            $this->saveFile($trainingContract, $request);

            $this->saveQuestions($trainingContract, $request->get('questions'));

            /*if ($request->has('documents'))
                $this->saveDocuments($request->documents, $trainingContract);*/

            //$this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la capacitación'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Training $trainingContract)
    {
        $fileBk = $trainingContract->replicate();

        if (!$trainingContract->delete())
            return $this->respondHttp500();

        if ($fileBk->file)
            Storage::disk('s3')->delete($fileBk->path_client(false)."/".$fileBk->file);
        
        return $this->respondHttp200([
            'message' => 'Se elimino la capacitación'
        ]);
    }

    private function saveQuestions($training, $questions)
    {
        foreach ($questions as $key => $question)
        {
            $id = isset($question['id']) ? $question['id'] : NULL;
            $config = collect(['options' => [], 'answers' => []]);

            if ($question['type_question_id'] == 1 || $question['type_question_id'] == 3)
            {
                $config->put('options', explode("\n", $question['options']));
                $config->put('answers', explode("\n", $question['answers']));
            }
            else
            {
                $config->put('answers', [$question['answers']]);
            }

            $question['answer_options'] = $config;

            $training->questions()->updateOrCreate(['id'=>$id], $question);
        }
    }

    public function saveFile($training, $request)
    {
        if ($request->file)
        {
            if ($request->file != $training->file)
            {
                if ($request->has('old_file') && $request->old_file)
                {
                    Storage::disk('s3')->delete($training->path_client(false)."/".$request->old_file);
                }
                
                $file = $request->file;
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs($training->path_client(false), $nameFile, 's3');
                $training->file = $nameFile;
                $training->save();
            }
        }
        
    }

    /*private function saveDocuments($documents, $activity)
    {
        foreach ($documents as $document)
        {
            $id = isset($document['id']) ? $document['id'] : NULL;
            $activity->documents()->updateOrCreate(['id'=>$id], $document);
        }
    }

    private function deleteData($data)
    {    
        if (COUNT($data['documents']) > 0)
            ActivityDocument::destroy($data['documents']);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    /*public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $activities = ActivityContract::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = ActivityContract::selectRaw("
                sau_ct_activities.id as id,
                sau_ct_activities.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function download(Training $trainingContract)
    {
      return Storage::disk('s3')->download($trainingContract->path_donwload());
    }

    public function multiselectTypeQuestion(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $typeQuestions = TrainingTypeQuestion::select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($typeQuestions)
            ]);
        }
        else
        {
            $typeQuestions = TrainingTypeQuestion::selectRaw("
                sau_ct_training_types_questions.id as id,
                sau_ct_training_types_questions.description as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($typeQuestions);
        }
    }
}
