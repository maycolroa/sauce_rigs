<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\Training;
use App\Http\Requests\LegalAspects\Contracts\TrainingRequest;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\TrainingQuestions;
use App\Models\LegalAspects\Contracts\TrainingTypeQuestion;
use App\Models\LegalAspects\Contracts\TrainingFiles;
use App\Jobs\LegalAspects\Contracts\Training\TrainingSendNotificationJob;
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
        $trainings = Training::select('*')->orderBy('id', 'DESC');

        return Vuetable::of($trainings)
                ->addColumn('retrySendMail', function ($training) {
                    return $training->isActive();
                })
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
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt')
                        {
                            $fail('Archivo debe ser un pdf, doc, docx, xlsx, xls, ppt, pptx');
                        }
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
            $training->max_calification = $request->number_questions_show;

            if (!$training->save())
                return $this->respondHttp500();

            $activity_all = '';

            if ($request->has('activity_id'))
            {
                if (count($request->activity_id) > 1)
                {
                    foreach ($request->activity_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todas')
                        {
                            $activity_all = 'Todas';
                            break;
                        }
                    }
                }
                else if (count($request->activity_id) == 1)
                    $activity_all =  json_decode($request->activity_id[0])->value;
            }

            if ($activity_all == 'Todas')
                $activities = $this->getActivitiesValues();
            else
                $activities = $this->getDataFromMultiselect($request->activity_id);

            $training->activities()->sync($activities);

            $this->saveFile($training, $request->get('files'));

            $this->saveQuestions($training, $request->get('questions'));  

            $this->saveLogActivitySystem('Contratistas - Capacitaciones', 'Se creo la capacitación '.$training->name);

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

            foreach ($training->questions as $question)
            {
                $question->key = Carbon::now()->timestamp + rand(1,10000);
                $question->multiselect_type_question_id = $question->type->multiselect();

                if ($question->type_question_id == 1 || $question->type_question_id == 3 || $question['type_question_id'] == 5)
                {
                    $question->options = implode("\n", $question->answer_options->get('options'));
                }

                $question->answers = implode("\n", $question->answer_options->get('answers'));
            }

            $training->files = $this->getFiles($training->id);

            $training->delete = [
                'questions' => [],
                'files' => []
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
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'pdf' && $ext != 'docx' && $ext != 'doc' && $ext != 'pptx' && $ext != 'ppt')
                        {
                            $fail('Archivo debe ser un pdf, doc, docx, xlsx, xls, ppt, pptx');
                        }
                    }
                }

            ]
        ])->validate();
        
        DB::beginTransaction();

        try
        {
            $trainingContract->fill($request->except('files'));
            $trainingContract->modifier_user = $this->user->id;
            $trainingContract->max_calification = $request->number_questions_show;

            if (!$trainingContract->update())
                return $this->respondHttp500();

            $activity_all = '';

            if ($request->has('activity_id'))
            {
                if (count($request->activity_id) > 1)
                {
                    foreach ($request->activity_id as $key => $value) 
                    {
                        $verify = json_decode($value)->value;
    
                        if ($verify == 'Todas')
                        {
                            $activity_all = 'Todas';
                            break;
                        }
                    }
                }
                else if (count($request->activity_id) == 1)
                    $activity_all =  json_decode($request->activity_id[0])->value;
            }

            if ($activity_all == 'Todas')
                $activitiesTraining = $this->getActivitiesValues();
            else
                $activitiesTraining = $this->getDataFromMultiselect($request->activity_id);

            $trainingContract->activities()->sync($activitiesTraining);

            $this->saveFile($trainingContract, $request->get('files'));

            $this->saveQuestions($trainingContract, $request->get('questions'));

            /*if ($request->has('documents'))
                $this->saveDocuments($request->documents, $trainingContract);*/

            $this->deleteData($request->get('delete'));

            $this->saveLogActivitySystem('Contratistas - Capacitaciones', 'Se edito la capacitación '.$trainingContract->name);

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

    private function getActivitiesValues()
    {
        $activities = ActivityContract::selectRaw(
            "sau_ct_activities.id as id")
        ->where('company_id', $this->company)
        ->pluck('id')
        ->toArray();

        return $activities;
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

        $file_delete = TrainingFiles::where('training_id', $trainingContract->id)->get();

        if ($file_delete)
        {
            foreach ($file_delete as $keyf => $file)
            {
                $path = $file->file;
                Storage::disk('s3')->delete('legalAspects/contracts/trainings/files/'. $path);
            }
        }

        $this->saveLogActivitySystem('Contratistas - Capacitaciones', 'Se elimino la capacitación '.$trainingContract->name);

        if (!$trainingContract->delete())
            return $this->respondHttp500();
        
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

            if ($question['type_question_id'] == 1 || $question['type_question_id'] == 3 || $question['type_question_id'] == 5)
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

    public function saveFile($training, $files)
    {
        if ($files && count($files) > 0)
        {
            $files_names_delete = [];

            foreach ($files as $keyF => $value) 
            {
                $create_file = true;

                if (isset($value['id']))
                {
                    $fileUpload = TrainingFiles::findOrFail($value['id']);

                    if ($value['type'] == 'Archivo')
                        if ($value['old_name'] == $value['file'] )
                            $create_file = false;
                    else
                        array_push($files_names_delete, $value['old_name']);
                }
                else
                {
                    $fileUpload = new TrainingFiles();                    
                    $fileUpload->training_id = $training->id;
                    $fileUpload->name = $value['name'];
                    $fileUpload->type = $value['type'];
                }

                if ($value['type'] == 'Link')
                {
                    $fileUpload->link = $value['link'];
                    $fileUpload->file = NULL;
                }

                if ($create_file && $value['type'] == 'Archivo')
                {
                    $file_tmp = $value['file'];
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
                    $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                    $fileUpload->file = $nameFile;
                    $fileUpload->type_file = strtolower($file_tmp->getClientOriginalExtension());
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
        
    }

    public function deleteData($delete)
    {
        foreach ($delete['files'] as $id)
        {
            $file_delete = TrainingFiles::find($id);

            if ($file_delete)
            {
                $path = $file_delete->file;
                $file_delete->delete();
                Storage::disk('s3')->delete('legalAspects/contracts/trainings/files/'. $path);
            }
        }

        foreach ($delete['questions'] as $id)
        {
            $question = TrainingQuestions::find($id);

            if ($question)
                $question->delete();
                
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FileUpload  $fileUpload
     * @return \Illuminate\Http\Response
     */
    public function download(TrainingFiles $file)
    {
      return Storage::disk('s3')->download($file->path_donwload(), $file->name.'.'.$file->type_file);
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
                ->orderBy('description')
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
            ")
            ->orderBy('description')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($typeQuestions);
        }
    }

    public function toggleState(Request $request, $id)
    {
        $training = Training::findOrFail($id);

        if ($training->questions->count() >= $training->number_questions_show)
            $data = ['active' => $training->toogleState()];
        else
        {
            return $this->respondWithError('El número de preguntas asociados a la capacitación es menor al número de preguntas a mostrar en el examen, debe completar la capacitación para asi poder activarla.');
        }

        if (!$training->update($data)) {
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Contratistas - Capacitaciones', 'Se cambio el estado a '.$training->active);
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado de la capacitación'
        ]);
    }

    public function sendNotification($id)
    {
        TrainingSendNotificationJob::dispatch($this->company, $id);
    }

    public function getFiles($training)
    {
        $get_files = TrainingFiles::where('training_id', $training)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->name = $get_file->name;
                $get_file->type = $get_file->type;
                $get_file->link = $get_file->link;
                $get_file->old_name = $get_file->file;

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
    }
}
