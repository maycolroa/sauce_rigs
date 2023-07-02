<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Activities\Activity;
use App\Models\IndustrialSecure\Dangers\Danger;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrixActivity;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\DangerMatrix\QualificationDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsAdministrativeControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEngineeringControls;
use App\Models\IndustrialSecure\DangerMatrix\TagsEpp;
use App\Models\IndustrialSecure\DangerMatrix\TagsAddFields;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use App\Models\IndustrialSecure\DangerMatrix\TagsSubstitution;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use App\Models\IndustrialSecure\DangerMatrix\TagsHistoryChange;
use App\Models\IndustrialSecure\DangerMatrix\ChangeHistory;
use App\Models\IndustrialSecure\DangerMatrix\AdditionalFields;
use App\Models\IndustrialSecure\DangerMatrix\AdditionalFieldsValues;
use App\Models\IndustrialSecure\DangerMatrix\QualificationType;
use App\Models\IndustrialSecure\DangerMatrix\HistoryQualificationChange;
use App\Http\Requests\IndustrialSecure\DangerMatrix\AddFieldsRequest;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixExportJob;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\DangerMatrixTrait;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixImportTemplateExcel;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixImportJob;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\Traits\Filtertrait;
use DB;

class DangerMatrixController extends Controller
{
    use DangerMatrixTrait, Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:dangerMatrix_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:dangerMatrix_r, {$this->team}");
        $this->middleware("permission:dangerMatrix_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:dangerMatrix_d, {$this->team}", ['only' => 'destroy']);
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
        $dangersMatrix = DangerMatrix::selectRaw("
            sau_dangers_matrix.*,
            DATE_FORMAT(sau_dangers_matrix.updated_at, '%Y-%m-%d') as date,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name as headquarter,
            sau_employees_areas.name as area,
            sau_employees_processes.name as process,
            sau_users.name as supervisor,            
            case when sau_dangers_matrix.approved is true then 'SI' else 'NO' end as approved"
        )
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_dangers_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_dangers_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_dangers_matrix.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
        ->join('sau_users', 'sau_users.id', 'sau_dangers_matrix.user_id');

        return Vuetable::of($dangersMatrix)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->rulesDangerMatrix($request);
        
        return $this->saveDangerMatrix($request);
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
            $dangerMatrix = DangerMatrix::findOrFail($id);

            if ($dangerMatrix->approved == true)
                $dangerMatrix->approved = 'SI';
            else
                $dangerMatrix->approved = 'NO';

            $dangerMatrix->activitiesRemoved = [];
            $dangerMatrix->locations = $this->prepareDataLocationForm($dangerMatrix);
            $dangerMatrix->changeHistory = '';

            $fields = AdditionalFields::get();

            foreach ($fields as $field)
            {
                $field->value = '';
            }

            foreach ($fields as $field)
            {
                $add_field = AdditionalFieldsValues::where('danger_matrix_id',$id)->where('field_id', $field->id)->first();

                $field->value = $add_field['value'];
            }

            $dangerMatrix->add_fields = $fields;

            foreach ($dangerMatrix->activities as $keyActivity => $itemActivity)
            {   
                $itemActivity->id = $itemActivity->id;
                $itemActivity->key = Carbon::now()->timestamp + rand(1,10000);
                $itemActivity->dangersRemoved = [];
                $itemActivity->multiselect_activity = $itemActivity->activity->multiselect();

                foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                {
                    $itemDanger->key = Carbon::now()->timestamp + rand(1,10000);
                    $itemDanger->multiselect_danger = $itemDanger->danger->multiselect();

                    $qualificationsData = [];

                    foreach ($itemDanger->qualifications as $keyQ => $itemQ)
                    {
                        $qualificationsData[$itemQ->type_id] = ["value_id"=>$itemQ->value_id, "type_id"=>$itemQ->type_id];
                    }

                    $itemDanger->qualificationsData = $qualificationsData;
                    $itemDanger->actionPlan = ActionPlan::model($itemDanger)->prepareDataComponent();
                }
            }

            $dangerMatrix->historial = true;

            return $this->respondHttp200([
                'data' => $dangerMatrix,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function getFieldAdd(Request $request, DangerMatrix $dangersMatrix)
    {
        $fields = AdditionalFields::get();

        foreach ($fields as $field)
        {
            $field->value = '';
        }

        return $this->respondHttp200([
            'data' => $fields
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  DangerMatrix $dangerMatrix
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DangerMatrix $dangersMatrix)
    {
        $this->rulesDangerMatrix($request, $dangersMatrix);
        
        return $this->saveDangerMatrix($request, $dangersMatrix);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DangerMatrix $dangerMatrix
     * @return \Illuminate\Http\Response
     */
    public function destroy(DangerMatrix $dangersMatrix)
    {
        DB::beginTransaction();

        try
        { 
            $keywords = $this->user->getKeywords();

            $confLocation = $this->getLocationFormConfModule();
            $details_log = 'Se elimino la matriz de peligros ubicada en:';

            foreach ($dangersMatrix->activities as $keyActivity => $itemActivity)
            {  
                foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                {
                    ActionPlan::model($itemDanger)->modelDeleteAll();
                }
            }

            if ($confLocation['regional'] == 'SI')
                $details_log = $details_log . $keywords['regional']. ': ' .  $dangersMatrix->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['headquarter']. ': ' .  $dangersMatrix->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['process']. ': ' .  $dangersMatrix->process->name;
            if ($confLocation['area'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['area']. ': ' .  $dangersMatrix->area->name;

            $this->saveLogActivitySystem('Matriz de peligros', 'Se elimino la matriz de peligros ubicada en '.$details_log.' ');

            if(!$dangersMatrix->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la matriz de peligro'
        ]);
    }

    public function saveFields(AddFieldsRequest $request)
    {
        DB::beginTransaction();

        try
        {
            if ($request->has('fields'))
            {
                if ($request->fields)
                {
                    foreach ($request->fields as $value)
                    {
                        $id = isset($value['id']) ? $value['id'] : NULL;
                        AdditionalFields::updateOrCreate(['id'=>$id], ['company_id'=>$this->company, 'name'=>$value['name']]);
                    }
                }
            }

            if ($request->has('delete'))
                $this->deleteData($request->delete);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se guardaron los campos'
        ]);
    }

    public function getAdditionalFiels()
    {
        $fields = AdditionalFields::get();

        foreach ($fields as $field)
        {
            $field->key = Carbon::now()->timestamp + rand(1,10000);
        }

        return $this->respondHttp200([
            'delete' => [],
            'fields' => $fields
        ]);
    }

    private function deleteData($data)
    {    
        if (COUNT($data) > 0)
            AdditionalFields::destroy($data);
    }

    /**
     * generates the array of validations for the hazard matrix
     * 
     * @param Illuminate\Http\Request $request
     * @param DangerMatrix $dangerMatrix
     */
    private function rulesDangerMatrix($request, $dangerMatrix = null)
    {
        foreach ($request->activities as $key => $value)
        {
            $data['activities'][$key] = json_decode($value, true);
            $request->merge($data);
        }

        if ($request->has('activitiesRemoved'))
        {
            foreach ($request->get('activitiesRemoved') as $key => $value)
            {
                $data2['activitiesRemoved'][$key] = json_decode($value, true);
                $request->merge($data2);
            }
        }

        if ($request->has('locations'))
        {
            $data3['locations'] = json_decode($request->get('locations'), true);
            $request->merge($data3);
        }

        if ($request->has('participants') && $request->get('participants'))
        {
            foreach ($request->get('participants') as $key => $value)
            {
                $data4['participants'][$key] = json_decode($value, true);
                $request->merge($data4);
            }
        }

        if ($request->has('changeHistory') && $request->get('changeHistory'))
        {
            foreach ($request->get('changeHistory') as $key => $value)
            {
                $data5['changeHistory'][$key] = json_decode($value, true);
                $request->merge($data5);
            }
        }

        if ($request->has('add_fields'))
        {
            foreach ($request->get('add_fields') as $key => $value)
            {
                $data6['add_fields'][$key] = json_decode($value, true);
                $request->merge($data6);
            }
        }

        $id = null;

        if ($dangerMatrix)
            $id = $dangerMatrix->id;

        $rules = [
            'name' => 'nullable|string|unique:sau_dangers_matrix,name,'.$id.',id,company_id,'.$this->company,
            'approved' => 'nullable',
            'participants' => 'nullable|array',
            'activities' => 'required|array',
            'activities.*.activity_id' => 'required|exists:sau_dm_activities,id',
            'activities.*.type_activity' => 'required',
            'activities.*.dangers' => 'required|array',
            'activities.*.dangers.*.danger_id' => 'required|exists:sau_dm_dangers,id',
            'activities.*.dangers.*.danger_generated' => 'required',
            'activities.*.dangers.*.possible_consequences_danger' => 'required|array',
            'activities.*.dangers.*.generating_source' => 'required',
            'activities.*.dangers.*.collaborators_quantity' => 'required|integer|min:0',
            'activities.*.dangers.*.esd_quantity' => 'required|integer|min:0',
            'activities.*.dangers.*.visitor_quantity' => 'required|integer|min:0',
            'activities.*.dangers.*.student_quantity' => 'required|integer|min:0',
            'activities.*.dangers.*.esc_quantity' => 'required|integer|min:0',
            'activities.*.dangers.*.observations' => 'nullable',
            'activities.*.dangers.*.existing_controls_engineering_controls' => 'nullable|array',
            'activities.*.dangers.*.existing_controls_substitution' => 'nullable|array',
            'activities.*.dangers.*.existing_controls_warning_signage' => 'nullable|array',
            'activities.*.dangers.*.existing_controls_administrative_controls' => 'nullable|array',
            'activities.*.dangers.*.existing_controls_epp' => 'nullable|array',
            'activities.*.dangers.*.legal_requirements' => 'required|in:SI,NO',
            'activities.*.dangers.*.quality_policies' => 'required|in:SI,NO',
            'activities.*.dangers.*.objectives_goals' => 'required|in:SI,NO',
            'activities.*.dangers.*.risk_acceptability' => 'required|in:SI,NO',
            'activities.*.dangers.*.intervention_measures_elimination' => 'nullable',
            'activities.*.dangers.*.intervention_measures_substitution' => 'nullable|array',
            'activities.*.dangers.*.intervention_measures_engineering_controls' => 'nullable|array',
            'activities.*.dangers.*.intervention_measures_warning_signage' => 'nullable|array',
            'activities.*.dangers.*.intervention_measures_administrative_controls' => 'nullable|array',
            'activities.*.dangers.*.intervention_measures_epp' => 'nullable|array',
            'activities.*.dangers.*.qualifications' => 'required|array',
            'activities.*.dangers.*.qualifications.*.value_id' => 'required',
        ];

        $messages = [];

        $rulesConfLocation = $this->getLocationFormRules();
        $rules = array_merge($rules, $rulesConfLocation);

        $rulesActionPlan = ActionPlan::prefixIndex('activities.*.dangers.*.')->getRules($request->all());
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $messages = array_merge($messages, $rulesActionPlan['messages']);


        if ($dangerMatrix)
        {
            if ($request->historial != 'false')
                $rules['changeHistory'] = 'required';
            else
                $rules['changeHistory'] = 'nullable';
        }
        
        return Validator::make($request->all(), $rules, $messages)->validate();
    }

    /**
     * create / update the danger matrix
     *
     * @param Illuminate\Http\Request $request
     * @param DangerMatrix $dangerMatrix
     * @return \Illuminate\Http\Response
     */
    private function saveDangerMatrix($request, $dangerMatrix = null)
    {

        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();
        
        DB::beginTransaction();

        try
        { 
            $details_log = '';

            if ($dangerMatrix)
            {
                $details_log = $details_log.'Se actualizo la matriz de peligros ubicada en: ';
                $msg = 'Se actualizo la matriz de peligro';

                if ($request->has('changeHistory') && $request->get('changeHistory'))
                {
                    $history_change = $this->tagsPrepare($request->get('changeHistory'));
                    $this->tagsSave($history_change, TagsHistoryChange::class);
    
                    $dangerMatrix->histories()->create([
                        'user_id' => $this->user->id,
                        'description' => $history_change->implode(',')
                    ]);
                }
            }
            else
            {
                $details_log = $details_log.'Se creo la matriz de peligros ubicada en: ';
                $msg = 'Se creo la matriz de peligro';
                $dangerMatrix = new DangerMatrix();
                $dangerMatrix->company_id = $this->company;
            }

            $dangerMatrix->name = $request->get('name');
            $dangerMatrix->user_id = $this->user->id;

            $approved = $request->get('approved');

            if($approved == 'SI')
                $dangerMatrix->approved = true;
            else
                $dangerMatrix->approved = false;

            $participants = $this->tagsPrepare($request->get('participants'));
            $this->tagsSave($participants, TagsParticipant::class);
            $dangerMatrix->participants = $participants->implode(',');
            
            if(!$dangerMatrix->save()){
                return $this->respondHttp500();
            }

            if (isset($request->add_fields))
            {
                foreach ($request->add_fields as $key => $value) 
                {
                    $fields_add = $this->tagsPrepare($value['value']);

                    $this->tagsSaveFields($fields_add, TagsAddFields::class, $value['id']);

                    $field_exist = AdditionalFieldsValues::updateOrCreate(['danger_matrix_id'=> $dangerMatrix->id, 'field_id' => $value['id']], [
                        'field_id' => $value['id'],
                        'danger_matrix_id' => $dangerMatrix->id,
                        'value' => $fields_add->implode(',')
                    ]);
                }
            }

            if($this->updateModelLocationForm($dangerMatrix, $request->get('locations')))
            {
                return $this->respondHttp500();
            }

            if ($request->has('activitiesRemoved') && COUNT($request->get('activitiesRemoved')) > 0)
            {
                foreach ($request->get('activitiesRemoved') as $key => $value)
                {
                    $activityDel = DangerMatrixActivity::find($value['id']);

                    if ($activityDel)
                        $activityDel->delete();
                }
            }

            //Se inician los atributos necesarios que seran estaticos para todas las actividades
            // De esta forma se evitar la asignacion innecesaria una y otra vez 
            ActionPlan::
                    user($this->user)
                ->module('dangerMatrix')
                ->regional($dangerMatrix->regional ? $dangerMatrix->regional->name : null)
                ->headquarter($dangerMatrix->headquarter ? $dangerMatrix->headquarter->name : null)
                ->area($dangerMatrix->area ? $dangerMatrix->area->name : null)
                ->process($dangerMatrix->process ? $dangerMatrix->process->name : null)
                ->creationDate($dangerMatrix->created_at)
                ->url(url('/industrialsecure/dangermatrix/view/'.$dangerMatrix->id));


            if ($confLocation['regional'] == 'SI')
                $details_log = $details_log . $keywords['regional']. ': ' .  $dangerMatrix->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['headquarter']. ': ' .  $dangerMatrix->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['process']. ': ' .  $dangerMatrix->process->name;
            if ($confLocation['area'] == 'SI')
                $details_log = $details_log . '- ' .$keywords['area']. ': ' .  $dangerMatrix->area->name;

            $this->saveLogActivitySystem('Matriz de peligros', $details_log);

            foreach ($request->get('activities') as $keyA => $itemA)
            {
                if ($itemA['id'] == '')
                {
                    $activity = new DangerMatrixActivity();
                    $activity->danger_matrix_id = $dangerMatrix->id;
                }
                else
                    $activity = DangerMatrixActivity::find($itemA['id']);

                $activity->activity_id = $itemA['activity_id'];
                $activity->type_activity = $itemA['type_activity'];

                if(!$activity->save()){
                    return $this->respondHttp500();
                }

                foreach ($itemA['dangers'] as $keyD => $itemD)
                {
                    $change = 0;
                    $observation_qualifications_old = '';

                    if ($itemD['id'] == '')
                        $danger = new ActivityDanger();
                    else
                    {    $danger = ActivityDanger::find($itemD['id']);
                        $observation_qualifications_old =  $danger->observation_qualifications;
                    }
                    
                    //TAGS
                    $possible_consequences_danger = $this->tagsPrepare($itemD['possible_consequences_danger']);
                    $danger_description = $this->tagsPrepare($itemD['danger_description']);
                    $existing_controls_engineering_controls = $this->tagsPrepare($itemD['existing_controls_engineering_controls']);
                    $existing_controls_substitution = $this->tagsPrepare($itemD['existing_controls_substitution']);
                    $existing_controls_warning_signage = $this->tagsPrepare($itemD['existing_controls_warning_signage']);
                    $existing_controls_administrative_controls = $this->tagsPrepare($itemD['existing_controls_administrative_controls']);
                    $existing_controls_epp = $this->tagsPrepare($itemD['existing_controls_epp']);
                    $intervention_measures_substitution = $this->tagsPrepare($itemD['intervention_measures_substitution']);
                    $intervention_measures_engineering_controls = $this->tagsPrepare($itemD['intervention_measures_engineering_controls']);
                    $intervention_measures_warning_signage = $this->tagsPrepare($itemD['intervention_measures_warning_signage']);
                    $intervention_measures_administrative_controls = $this->tagsPrepare($itemD['intervention_measures_administrative_controls']);
                    $intervention_measures_epp = $this->tagsPrepare($itemD['intervention_measures_epp']);

                    $this->tagsSave($possible_consequences_danger, TagsPossibleConsequencesDanger::class);
                    $this->tagsSave($danger_description, TagsDangerDescription::class);
                    $this->tagsSave($existing_controls_engineering_controls, TagsEngineeringControls::class);
                    $this->tagsSave($existing_controls_substitution, TagsSubstitution::class);
                    $this->tagsSave($existing_controls_warning_signage, TagsWarningSignage::class);
                    $this->tagsSave($existing_controls_administrative_controls, TagsAdministrativeControls::class);
                    $this->tagsSave($existing_controls_epp, TagsEpp::class);
                    $this->tagsSave($intervention_measures_substitution, TagsSubstitution::class);
                    $this->tagsSave($intervention_measures_engineering_controls, TagsEngineeringControls::class);
                    $this->tagsSave($intervention_measures_warning_signage, TagsWarningSignage::class);
                    $this->tagsSave($intervention_measures_administrative_controls, TagsAdministrativeControls::class);
                    $this->tagsSave($intervention_measures_epp, TagsEpp::class);

                    //////////////////////////////
                    
                    $danger->dm_activity_id = $activity->id;
                    $danger->danger_id = $itemD['danger_id'];
                    $danger->danger_description = $danger_description->implode(',');
                    $danger->danger_generated = $itemD['danger_generated'];
                    $danger->possible_consequences_danger = $possible_consequences_danger->implode(',');
                    $danger->generating_source = $itemD['generating_source'];
                    $danger->collaborators_quantity = $itemD['collaborators_quantity'];
                    $danger->esd_quantity = $itemD['esd_quantity'];
                    $danger->visitor_quantity = $itemD['visitor_quantity'];
                    $danger->student_quantity = $itemD['student_quantity'];
                    $danger->esc_quantity = $itemD['esc_quantity'];            
                    $danger->observations = $itemD['observations'];
                    $danger->existing_controls_engineering_controls = $existing_controls_engineering_controls->implode(',');
                    $danger->existing_controls_substitution = $existing_controls_substitution->implode(',');
                    $danger->existing_controls_warning_signage = $existing_controls_warning_signage->implode(',');
                    $danger->existing_controls_administrative_controls = $existing_controls_administrative_controls->implode(',');
                    $danger->existing_controls_epp = $existing_controls_epp->implode(',');
                    $danger->legal_requirements = $itemD['legal_requirements'];
                    $danger->quality_policies = $itemD['quality_policies'];
                    $danger->objectives_goals = $itemD['objectives_goals'];
                    $danger->risk_acceptability = $itemD['risk_acceptability'];
                    $danger->intervention_measures_elimination = $itemD['intervention_measures_elimination'];
                    $danger->intervention_measures_substitution = $intervention_measures_substitution->implode(',');
                    $danger->intervention_measures_engineering_controls = $intervention_measures_engineering_controls->implode(',');
                    $danger->intervention_measures_warning_signage = $intervention_measures_warning_signage->implode(',');
                    $danger->intervention_measures_administrative_controls = $intervention_measures_administrative_controls->implode(',');
                    $danger->intervention_measures_epp = $intervention_measures_epp->implode(',');
                    $danger->observation_qualifications = isset($itemD['observation_qualifications']) ? $itemD['observation_qualifications'] : null;

                    if(!$danger->save()){
                        return $this->respondHttp500();
                    }

                    $qualification_old_value = $danger->qualification;

                    $qualification_old = QualificationDanger::where('activity_danger_id', $danger->id)->get();

                    QualificationDanger::where('activity_danger_id', $danger->id)->delete();

                    $conf = QualificationCompany::select('qualification_id')->first();

                    if ($conf && $conf->qualification)
                        $conf = $conf->qualification->name;
                    else
                        $conf = $this->getDefaultCalificationDm();

                    foreach ($itemD['qualifications'] as $itemQ)
                    {
                        $qualification = new QualificationDanger();
                        $qualification->activity_danger_id = $danger->id;
                        $qualification->type_id = $itemQ['type_id'];
                        $qualification->value_id = $itemQ['value_id'];

                        if ($conf == 'Tipo 1')
                        {
                            if ($itemQ['description'] == 'NRI')
                                $nri = $itemQ['value_id'];

                            if ($itemQ['description'] == 'Nivel de Probabilidad')
                                $ndp = $itemQ['value_id'];
                        }

                        else if ($conf == 'Tipo 2')
                        {
                            if ($itemQ['description'] == 'Severidad')
                                $sev = $itemQ['value_id'];

                            if ($itemQ['description'] == 'Frecuencia')
                                $fre = $itemQ['value_id'];
                        }

                        if (!$qualification->save())
                            return $this->respondHttp500();

                        $var = $qualification_old->where('type_id', $itemQ['type_id'])->first();

                        if ($var && $var->value_id)
                        {
                            if ($var->value_id != $qualification->value_id)
                                $change++;
                        }

                    }

                    $qualification_new = QualificationDanger::where('activity_danger_id', $danger->id)->get();

                    if ($conf == 'Tipo 1')
                    {
                        $matriz_calification = $this->getMatrixCalification($conf);

                        if (isset($matriz_calification[$ndp]) && isset($matriz_calification[$ndp][$nri]))
                        {
                            $danger->qualification = $matriz_calification[$ndp][$nri]['label'];
                            $danger->save();
                        }
                    }
                    
                    else if ($conf == 'Tipo 2')
                    {
                        $matriz_calification = $this->getMatrixCalification($conf);

                        if (isset($matriz_calification[$sev]) && isset($matriz_calification[$sev][$fre]))
                        {
                            $danger->qualification = $matriz_calification[$sev][$fre]['label'];
                            $danger->save();
                        }
                    }

                    $activity_procedence = Activity::find($itemA['activity_id']);
                    $danger_procedence = Danger::find($danger->danger_id);

                    $detail_procedence = 'Mátriz de Peligros - Actividad: '. $activity_procedence->name . '. Peligro: '. $danger_procedence->name;

                    /**Planes de acción*/
                    ActionPlan::
                          model($danger)
                        ->activities($itemD['actionPlan'])
                        ->detailProcedence($detail_procedence)
                        ->save();
                    
                    if ($observation_qualifications_old != $danger->observation_qualifications)
                        $change++;

                    if ($change > 0)
                    {
                        $infor_log_qualification = [
                            'old' => $qualification_old->toArray(),
                            'new' => $qualification_new->toArray(),
                            'observations_old' =>  $observation_qualifications_old,
                            'observations_new' => $danger->observation_qualifications,
                            'calification_old' =>  $qualification_old_value,
                            'calification_new' => $danger->qualification,
                            'activity_danger' => $danger->id,
                            'activity' => $activity_procedence->id,
                            'danger' => $danger_procedence->id,
                            'matrix' => $dangerMatrix->id,
                        ];

                        $this->saveLogQualification($infor_log_qualification);
                    }

                }

                if (isset($itemA['dangersRemoved']) && COUNT($itemA['dangersRemoved']) > 0)
                {
                    foreach ($itemA['dangersRemoved'] as $key => $value)
                    {
                        $dangerDel = ActivityDanger::find($value['id']);

                        if ($dangerDel)
                        {
                            ActionPlan::model($dangerDel)->modelDeleteAll();
                            $dangerDel->delete();
                        }
                    }
                }
            }

            ActionPlan::sendMail();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //$msg = $e->getMessage();
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }


        return $this->respondHttp200([
            'data' => $this->show($dangerMatrix->id)
        ]);

        return $this->respondHttp200([
            'message' => $msg
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $danger_matrix = DangerMatrix::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($danger_matrix)
            ]);
        }
        else
        {
            $danger_matrix = DangerMatrix::selectRaw("
                sau_dangers_matrix.id as id,
                sau_dangers_matrix.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($danger_matrix);
        }
    }

    public function download(DangerMatrix $dangersMatrix)
    {
        try
        {
            DangerMatrixExportJob::dispatch($this->user, $this->company, $dangersMatrix->id);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function downloadTemplateImport()
    {
      return Excel::download(new DangerMatrixImportTemplateExcel(collect([]), $this->company), 'PlantillaImportacionMatrizPeligro.xlsx');
    }

    public function import(Request $request)
    {
      try
      {
        DangerMatrixImportJob::dispatch($request->file, $this->company, $this->user);
      
        return $this->respondHttp200();

      } catch(Exception $e)
      {
        return $this->respondHttp500();
      }
    }

    public function saveLogQualification($infor)
    {
        $qualification_old = collect([]);
        $qualification_new = collect([]);

        foreach($infor['old'] as $value)
        {
            $qualification_old->push([
                "name" => QualificationType::find($value['type_id'])->description,
                "value" => $value['value_id']
            ]);
        }

        $qualification_old->push([
            "name" => 'Calificacion',
            "value" => $infor['calification_old']
        ]);

        $qualification_old->push([
            "name" => 'Observaciones',
            "value" => $infor['observations_old']
        ]);

        foreach($infor['new'] as $value)
        {
            $qualification_new->push([
                "name" => QualificationType::find($value['type_id'])->description,
                "value" => $value['value_id']
            ]);
        }
        
        $qualification_new->push([
            "name" => 'Calificacion',
            "value" => $infor['calification_new']
        ]);

        $qualification_new->push([
            "name" => 'Observaciones',
            "value" => $infor['observations_new']
        ]);

        $history = new HistoryQualificationChange;
        $history->company_id = $this->company;
        $history->user_id = $this->user->id;
        $history->danger_matrix_id = $infor['matrix'];
        $history->activity_id = $infor['activity'];
        $history->danger_id = $infor['danger'];
        $history->activity_danger_id = $infor['activity_danger'];
        $history->qualification_old = json_encode($qualification_old->toArray());
        $history->qualification_new = json_encode($qualification_new->toArray());
        $history->save();
    }

    public function getLogQualificationChange(Request $request)
    {
        $url = 'industrialsecure/dangermatrix/logQualification';

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);
        $regionals = [];
        $headquarters = [];
        $areas = [];
        $processes = [];
        $dangers = [];
        $activities = [];

        if (COUNT($filters) > 0)
        {
            /** FIltros */
            $regionals = isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : [];
            
            $headquarters = isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : [];
            
            $areas = isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : [];
            
            $processes = isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : [];
            
            $dangers = isset($filters['dangers']) ? $this->getValuesForMultiselect($filters['dangers']) : [];

            $activities = isset($filters['activities']) ? $this->getValuesForMultiselect($filters['activities']) : [];
        }

        $histories = HistoryQualificationChange::select(
            'sau_dm_activities.name as activity',
            'sau_dm_dangers.name as danger',
            'sau_users.name as user',
            'sau_dangers_matrix.name as matriz',
            DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_old, '$[1].value')) as nr_persona_old"),
            DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_old, '$[5].value')) as qualification_old2"),
            //DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_old, '$[6].value')) as observation_old2"),
            DB::raw("IFNULL(JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_old, '$[6].value')), '') as observation_old2"),
            DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_new, '$[1].value')) as nr_persona_new"),
            DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_new, '$[5].value')) as qualification_new2"),
            //DB::raw("JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_new, '$[6].value')) as observation_new2"),
            DB::raw("IFNULL(JSON_UNQUOTE(json_extract(sau_dm_history_qualification_change.qualification_new, '$[6].value')), '') as observation_new2"),
            Db::raw("DATE_FORMAT(sau_dm_history_qualification_change.created_at, '%Y-%m-%d') as fecha")
        )
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_dm_history_qualification_change.danger_matrix_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_dm_history_qualification_change.activity_id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_history_qualification_change.danger_id')
        ->join('sau_users', 'sau_users.id', 'sau_dm_history_qualification_change.user_id')
        ->inRegionals($regionals, isset($filtersType['regionals']) ? $filters['filtersType']['regionals'] : 'IN')
        ->inHeadquarters($headquarters, isset($filtersType['headquarters']) ? $filters['filtersType']['headquarters'] : 'IN')
        ->inAreas($areas, isset($filters['filtersType']['areas']) ? $filters['filtersType']['areas'] : 'IN')
        ->inProcesses($processes, isset($filters['filtersType']['processes']) ? $filters['filtersType']['processes'] : 'IN')
        ->inDangers($dangers, isset($filters['filtersType']['dangers']) ? $filters['filtersType']['dangers'] : 'IN')
        ->inActivities($activities, isset($filters['filtersType']['activities']) ? $filters['filtersType']['activities'] : 'IN');

        return Vuetable::of($histories)
                    ->make();
    }

    public function searchKeyword(Request $request)
    {        
        $generating_source = DangerMatrix::selectRaw("
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Fuente Generadora' campo,  
            sau_dm_activity_danger.generating_source as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dangers_matrix.id', $request->danger_matrix)
        ->where('sau_dm_activity_danger.generating_source', 'like', "%$request->keyword%");


        $possible_consequences_danger = DangerMatrix::selectRaw("
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Posibles consecuencias del peligro' campo,  
            sau_dm_activity_danger.possible_consequences_danger as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dangers_matrix.id', $request->danger_matrix)
        ->where('sau_dm_activity_danger.possible_consequences_danger', 'like', "%$request->keyword%");

        $existing_controls_administrative_controls = DangerMatrix::selectRaw("
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Administrativos' campo,  
            sau_dm_activity_danger.existing_controls_administrative_controls as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dangers_matrix.id', $request->danger_matrix)
        ->where('sau_dm_activity_danger.existing_controls_administrative_controls', 'like', "%$request->keyword%");

        $generating_source->union($possible_consequences_danger);
        $generating_source->union($existing_controls_administrative_controls);

        return Vuetable::of($generating_source)
                    ->make();
    }
}
