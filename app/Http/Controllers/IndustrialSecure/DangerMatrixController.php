<?php

namespace App\Http\Controllers\IndustrialSecure;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\IndustrialSecure\DangerMatrix;
use App\IndustrialSecure\DangerMatrixActivity;
use App\IndustrialSecure\ActivityDanger;
use App\IndustrialSecure\QualificationMethodologie;
use App\IndustrialSecure\ConfigQualificationMethodologie;
use App\IndustrialSecure\TagsAdministrativeControls;
use App\IndustrialSecure\TagsEngineeringControls;
use App\IndustrialSecure\TagsEpp;
use App\IndustrialSecure\TagsPossibleConsequencesDanger;
use App\IndustrialSecure\TagsWarningSignage;
use App\IndustrialSecure\ChangeHistory;
use Illuminate\Support\Facades\Auth;
use App\Administrative\Configurations\LocationLevelForm;
use Session;
use Validator;
use DB;

class DangerMatrixController extends Controller
{
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
        $dangersMatrix = DangerMatrix::select('*');

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
                $itemActivity->dangersRemoved = [];
                $itemActivity->multiselect_activity = $itemActivity->activity->multiselect();

                foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                {
                    $itemDanger->multiselect_danger = $itemDanger->danger->multiselect();

                    $qualificationsData = [];

                    foreach ($itemDanger->qualifications as $keyQ => $itemQ)
                    {
                        $qualificationsData[$itemQ->type] = ["qualification"=>$itemQ->qualification, "type"=>$itemQ->type];
                    }

                    $itemDanger->qualificationsData = $qualificationsData;
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
        if(!$dangersMatrix->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la matriz de peligro'
        ]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getConfigQualificationMethodologies()
    {
        $config = ConfigQualificationMethodologie::select('types', 'qualifications')->first();
        $data = [
            "types" => [],
            "qualifications" => [],
            "help" => ""
        ];

        if ($config)
        {
            foreach (json_decode($config->types, true) as $key => $value) 
            {
                array_push($data["types"], $value["value"]);
            }

            $tmp = [];
            $help = "";

            foreach (json_decode($config->qualifications, true) as $key => $value)
            {
                array_push($tmp, $value["value"]);
                $help .= $value["value"].'. '.$value["description"]."\n";
            }

            $data["qualifications"] = $this->multiSelectFormat($tmp);
            $data["help"] = $help;
        }

        return $data;
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
            'activities.*.dangers.*.qualifications.*.qualification' => 'required',
        ];

        $rulesConfLocation = $this->getLocationFormRules('industrialSecure', 'dangerMatrix');

        $newRules = array_merge($rules, $rulesConfLocation);

        if ($dangerMatrix)
            $newRules['changeHistory'] = 'required';

        return Validator::make($request->all(), $newRules)->validate();
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

            if($this->updateModelLocationForm('industrialSecure', 'dangerMatrix', $dangerMatrix, $request->get('locations')))
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

                    QualificationMethodologie::where('activity_danger_id', $danger->id)->delete();

                    foreach ($itemD['qualifications'] as $itemQ)
                    {
                        $qualification = new QualificationMethodologie();
                        $qualification->activity_danger_id = $danger->id;
                        $qualification->type = $itemQ['type'];
                        $qualification->qualification = $itemQ['qualification'];

                        if(!$qualification->save()){
                            return $this->respondHttp500();
                        }
                    }
                }

                if (isset($itemA['dangersRemoved']) && COUNT($itemA['dangersRemoved']) > 0)
                {
                    foreach ($itemA['dangersRemoved'] as $key => $value)
                    {
                        $dangerDel = ActivityDanger::find($value['id']);

                        if ($dangerDel)
                            $dangerDel->delete();
                    }
                }
            }

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
