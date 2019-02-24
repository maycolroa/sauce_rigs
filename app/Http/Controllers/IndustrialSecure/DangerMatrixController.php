<?php

namespace App\Http\Controllers\IndustrialSecure;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\IndustrialSecure\DangerMatrix;
use App\IndustrialSecure\DangerMatrixActivity;
use App\IndustrialSecure\ActivityDanger;
use App\IndustrialSecure\QualificationDanger;
use App\IndustrialSecure\TagsAdministrativeControls;
use App\IndustrialSecure\TagsEngineeringControls;
use App\IndustrialSecure\TagsEpp;
use App\IndustrialSecure\TagsPossibleConsequencesDanger;
use App\IndustrialSecure\TagsWarningSignage;
use App\IndustrialSecure\ChangeHistory;
use Illuminate\Support\Facades\Auth;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Carbon\Carbon;
use Session;
use Validator;
use DB;

class DangerMatrixController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dangerMatrix_c', ['only' => 'store']);
        $this->middleware('permission:dangerMatrix_r');
        $this->middleware('permission:dangerMatrix_u', ['only' => 'update']);
        $this->middleware('permission:dangerMatrix_d', ['only' => 'destroy']);
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
        $dangersMatrix = DangerMatrix::select(
            'sau_dangers_matrix.*',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name as headquarter',
            'sau_employees_areas.name as area',
            'sau_employees_processes.name as process',
            'sau_users.name as supervisor'
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
            $dangerMatrix->activitiesRemoved = [];
            $dangerMatrix->locations = $this->prepareDataLocationForm($dangerMatrix);
            $dangerMatrix->changeHistory = '';

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

        $id = null;

        if ($dangerMatrix)
            $id = $dangerMatrix->id;

        $rules = [
            'name' => 'required|string|unique:sau_dangers_matrix,name,'.$id.',id,company_id,'.Session::get('company_id'),
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
            'activities.*.dangers.*.existing_controls_engineering_controls' => 'required|array',
            'activities.*.dangers.*.existing_controls_substitution' => 'required',
            'activities.*.dangers.*.existing_controls_warning_signage' => 'required|array',
            'activities.*.dangers.*.existing_controls_administrative_controls' => 'required|array',
            'activities.*.dangers.*.existing_controls_epp' => 'required|array',
            'activities.*.dangers.*.legal_requirements' => 'required|in:SI,NO',
            'activities.*.dangers.*.quality_policies' => 'required|in:SI,NO',
            'activities.*.dangers.*.objectives_goals' => 'required|in:SI,NO',
            'activities.*.dangers.*.risk_acceptability' => 'required|in:SI,NO',
            'activities.*.dangers.*.intervention_measures_elimination' => 'required',
            'activities.*.dangers.*.intervention_measures_substitution' => 'required',
            'activities.*.dangers.*.intervention_measures_engineering_controls' => 'required|array',
            'activities.*.dangers.*.intervention_measures_warning_signage' => 'required|array',
            'activities.*.dangers.*.intervention_measures_administrative_controls' => 'required|array',
            'activities.*.dangers.*.intervention_measures_epp' => 'required|array',
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

                $dangerMatrix->histories()->create([
                    'user_id' => Auth::user()->id,
                    'description' => $request->get('changeHistory')
                ]);
            }
            else
            {
                $msg = 'Se creo la matriz de peligro';
                $dangerMatrix = new DangerMatrix();
                $dangerMatrix->company_id = Session::get('company_id');
                $dangerMatrix->user_id = Auth::user()->id;
            }

            $dangerMatrix->name = $request->get('name');
            
            if(!$dangerMatrix->save()){
                return $this->respondHttp500();
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
                    user(Auth::user())
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
                    $existing_controls_engineering_controls = $this->tagsPrepare($itemD['existing_controls_engineering_controls']);
                    $existing_controls_warning_signage = $this->tagsPrepare($itemD['existing_controls_warning_signage']);
                    $existing_controls_administrative_controls = $this->tagsPrepare($itemD['existing_controls_administrative_controls']);
                    $existing_controls_epp = $this->tagsPrepare($itemD['existing_controls_epp']);
                    $intervention_measures_engineering_controls = $this->tagsPrepare($itemD['intervention_measures_engineering_controls']);
                    $intervention_measures_warning_signage = $this->tagsPrepare($itemD['intervention_measures_warning_signage']);
                    $intervention_measures_administrative_controls = $this->tagsPrepare($itemD['intervention_measures_administrative_controls']);
                    $intervention_measures_epp = $this->tagsPrepare($itemD['intervention_measures_epp']);

                    $this->tagsSave($possible_consequences_danger, TagsPossibleConsequencesDanger::class);
                    $this->tagsSave($existing_controls_engineering_controls, TagsEngineeringControls::class);
                    $this->tagsSave($existing_controls_warning_signage, TagsWarningSignage::class);
                    $this->tagsSave($existing_controls_administrative_controls, TagsAdministrativeControls::class);
                    $this->tagsSave($existing_controls_epp, TagsEpp::class);
                    $this->tagsSave($intervention_measures_engineering_controls, TagsEngineeringControls::class);
                    $this->tagsSave($intervention_measures_warning_signage, TagsWarningSignage::class);
                    $this->tagsSave($intervention_measures_administrative_controls, TagsAdministrativeControls::class);
                    $this->tagsSave($intervention_measures_epp, TagsEpp::class);

                    //////////////////////////////
                    
                    $danger->dm_activity_id = $activity->id;
                    $danger->danger_id = $itemD['danger_id'];
                    $danger->danger_generated = $itemD['danger_generated'];
                    $danger->possible_consequences_danger = $possible_consequences_danger->implode(',');
                    $danger->generating_source = $itemD['generating_source'];
                    $danger->collaborators_quantity = $itemD['collaborators_quantity'];
                    $danger->esd_quantity = $itemD['esd_quantity'];
                    $danger->visitor_quantity = $itemD['visitor_quantity'];
                    $danger->student_quantity = $itemD['student_quantity'];
                    $danger->esc_quantity = $itemD['esc_quantity'];
                    $danger->existing_controls_engineering_controls = $existing_controls_engineering_controls->implode(',');
                    $danger->existing_controls_substitution = $itemD['existing_controls_substitution'];
                    $danger->existing_controls_warning_signage = $existing_controls_warning_signage->implode(',');
                    $danger->existing_controls_administrative_controls = $existing_controls_administrative_controls->implode(',');
                    $danger->existing_controls_epp = $existing_controls_epp->implode(',');
                    $danger->legal_requirements = $itemD['legal_requirements'];
                    $danger->quality_policies = $itemD['quality_policies'];
                    $danger->objectives_goals = $itemD['objectives_goals'];
                    $danger->risk_acceptability = $itemD['risk_acceptability'];
                    $danger->intervention_measures_elimination = $itemD['intervention_measures_elimination'];
                    $danger->intervention_measures_substitution = $itemD['intervention_measures_substitution'];
                    $danger->intervention_measures_engineering_controls = $intervention_measures_engineering_controls->implode(',');
                    $danger->intervention_measures_warning_signage = $intervention_measures_warning_signage->implode(',');
                    $danger->intervention_measures_administrative_controls = $intervention_measures_administrative_controls->implode(',');
                    $danger->intervention_measures_epp = $intervention_measures_epp->implode(',');

                    if(!$danger->save()){
                        return $this->respondHttp500();
                    }

                    QualificationDanger::where('activity_danger_id', $danger->id)->delete();

                    foreach ($itemD['qualifications'] as $itemQ)
                    {
                        $qualification = new QualificationDanger();
                        $qualification->activity_danger_id = $danger->id;
                        $qualification->type_id = $itemQ['type_id'];
                        $qualification->value_id = $itemQ['value_id'];

                        if(!$qualification->save()){
                            return $this->respondHttp500();
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
            //$msg = $e->getMessage();
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => $msg
        ]);
    }

    
}
