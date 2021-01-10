<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
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
use App\Http\Requests\IndustrialSecure\DangerMatrix\AddFieldsRequest;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixExportJob;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\DangerMatrixTrait;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixImportTemplateExcel;
use App\Jobs\IndustrialSecure\DangerMatrix\DangerMatrixImportJob;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use DB;

class DangerMatrixController extends Controller
{
    use DangerMatrixTrait;

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
            foreach ($dangersMatrix->activities as $keyActivity => $itemActivity)
            {  
                foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                {
                    ActionPlan::model($itemDanger)->modelDeleteAll();
                }
            }

            if(!$dangersMatrix->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
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

        $rulesActionPlan = ActionPlan::prefixIndex('activities.*.dangers.*.')->getRules();
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $messages = array_merge($messages, $rulesActionPlan['messages']);


        if ($dangerMatrix)
            $rules['changeHistory'] = 'required';
        
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
        DB::beginTransaction();

        try
        { 
            if ($dangerMatrix)
            {
                $msg = 'Se actualizo la matriz de peligro';

                $history_change = $this->tagsPrepare($request->get('changeHistory'));
                $this->tagsSave($history_change, TagsHistoryChange::class);

                $dangerMatrix->histories()->create([
                    'user_id' => $this->user->id,
                    'description' => $history_change->implode(',')
                ]);
            }
            else
            {
                $msg = 'Se creo la matriz de peligro';
                $dangerMatrix = new DangerMatrix();
                $dangerMatrix->company_id = $this->company;
                $dangerMatrix->user_id = $this->user->id;
            }

            $dangerMatrix->name = $request->get('name');

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
                    if ($itemD['id'] == '')
                        $danger = new ActivityDanger();
                    else
                        $danger = ActivityDanger::find($itemD['id']);
                    
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

                    if(!$danger->save()){
                        return $this->respondHttp500();
                    }

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
                    }

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

                    /**Planes de acción*/
                    ActionPlan::
                          model($danger)
                        ->activities($itemD['actionPlan'])
                        ->save();
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
}
