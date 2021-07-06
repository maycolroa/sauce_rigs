<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Http\Requests\IndustrialSecure\RiskMatrix\RiskMatrixRequest;
use App\Models\IndustrialSecure\RiskMatrix\TagsRmParticipant;
use App\Models\IndustrialSecure\RiskMatrix\TagsRmCauseControls;
use App\Models\IndustrialSecure\RiskMatrix\Cause;
use App\Models\IndustrialSecure\RiskMatrix\CauseControl;
use App\Models\IndustrialSecure\RiskMatrix\Indicators;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrixSubProcess;
use App\Models\IndustrialSecure\RiskMatrix\SubProcessRisk;
use App\Models\IndustrialSecure\RiskMatrix\Risk;
use App\Models\IndustrialSecure\RiskMatrix\SubProcess;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Processes\TagsProcess;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use Carbon\Carbon;
use DB;
use App\Traits\RiskMatrixTrait;


class RiskMatrixController extends Controller
{
    use RiskMatrixTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:regionals_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:regionals_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:regionals_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:regionals_d, {$this->team}", ['only' => 'destroy']);
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
        $riskMatrix = RiskMatrix::selectRaw("
            sau_rm_risks_matrix.*,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name as headquarter,
            sau_employees_areas.name as area,
            sau_employees_processes.name as process,
            sau_users.name as supervisor,            
            case when sau_rm_risks_matrix.approved is true then 'SI' else 'NO' end as approved"
        )
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
        ->join('sau_users', 'sau_users.id', 'sau_rm_risks_matrix.user_id')
        ->where('sau_rm_risks_matrix.company_id', $this->company);

        return Vuetable::of($riskMatrix)
                    ->make();
    }

    public function store(RiskMatrixRequest $request)
    {
        return $this->saveRiskMatrix($request);
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
            $riskMatrix = RiskMatrix::findOrFail($id);

            if ($riskMatrix->approved == true)
                $riskMatrix->approved = 'SI';
            else
                $riskMatrix->approved = 'NO';

            $riskMatrix->locations = $this->prepareDataLocationForm($riskMatrix);

            foreach ($riskMatrix->subprocesses as $keySubprocess=> $itemSubprocess)
            {   
                $itemSubprocess->key = Carbon::now()->timestamp + rand(1,10000);
                $itemSubprocess->subprocessesRemoved = [];
                $itemSubprocess->multiselect_subprocess = $itemSubprocess->subProcess->multiselect();

                foreach ($itemSubprocess->risks as $keyRisk => $itemRisk)
                {
                    $itemRisk->key = Carbon::now()->timestamp + rand(1,10000);
                    $itemRisk->riskRemoved = [];
                    $itemRisk->multiselect_risk = $itemRisk->risk->multiselect();

                    $itemRisk->actionPlan = ActionPlan::model($itemRisk)->prepareDataComponent();                    

                    $causes_controls = $itemRisk->causes->transform(function($cause, $index) {
                            $cause->key = Carbon::now()->timestamp + rand(1,10000);
                            $cause->cause = $cause->cause;
                            $cause->controls = $cause->controls()->orderBy('number_control')->get();
            
                        return $cause;
                    });

                    $itemRisk->indicators = $itemRisk->indicators;

                    $itemRisk->causes_controls = $causes_controls;
                }
            }

            return $this->respondHttp200([
                'data' => $riskMatrix,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function update(RiskMatrixRequest $request, RiskMatrix $risksMatrix)
    {        
        return $this->saveRiskMatrix($request, $risksMatrix);
    }

    public function destroy(RiskMatrix $risksMatrix)
    {
        DB::beginTransaction();

        try
        { 
            /*foreach ($dangersMatrix->activities as $keyActivity => $itemActivity)
            {  
                foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                {
                    ActionPlan::model($itemDanger)->modelDeleteAll();
                }
            }*/

            if(!$risksMatrix->delete())
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
            'message' => 'Se elimino la matriz de riesgos'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    private function saveRiskMatrix($request, $riskMatrix = null)
    {
        DB::beginTransaction();

        try
        { 
            if ($riskMatrix)
            {
                $msg = 'Se actualizo la matriz de riesgos';
           }
            else
            {
                $msg = 'Se creo la matriz de riesgos';
                $riskMatrix = new RiskMatrix();
                $riskMatrix->company_id = $this->company;
                $riskMatrix->user_id = $this->user->id;
            }

            $riskMatrix->name = $request->get('name');

            $approved = $request->get('approved');

            if($approved == 'SI')
                $riskMatrix->approved = true;
            else
                $riskMatrix->approved = false;

            $participants = $this->tagsPrepare($request->get('participants'));
            $this->tagsSave($participants, TagsRmParticipant::class);
            $riskMatrix->participants = $participants->implode(',');
            $riskMatrix->macroprocess_id = $request->locations['macroprocess_id'];
            $riskMatrix->nomenclature = $request->locations['nomenclature'];
            
            if(!$riskMatrix->save()){
                return $this->respondHttp500();
            }

            if($this->updateModelLocationForm($riskMatrix, $request->get('locations')))
            {
                return $this->respondHttp500();
            }

            if ($request->has('subprocessesRemoved') && COUNT($request->get('subprocessesRemoved')) > 0)
            {
                foreach ($request->get('subprocessesRemoved') as $key => $value)
                {
                    $subprocessDel = RiskMatrixSubProcess::find($value['id']);

                    if ($subprocessDel)
                        $subprocessDel->delete();
                }
            }

            //Se inician los atributos necesarios que seran estaticos para todos los subprocesos
            // De esta forma se evitar la asignacion innecesaria una y otra vez 
            ActionPlan::
                    user($this->user)
                ->module('riskMatrix')
                ->regional($riskMatrix->regional ? $riskMatrix->regional->name : null)
                ->headquarter($riskMatrix->headquarter ? $riskMatrix->headquarter->name : null)
                ->area($riskMatrix->area ? $riskMatrix->area->name : null)
                ->process($riskMatrix->process ? $riskMatrix->process->name : null)
                ->creationDate($riskMatrix->created_at)
                ->url(url('/industrialsecure/riskMatrix/view/'.$riskMatrix->id));

            foreach ($request->get('subprocesses') as $keyA => $itemS)
            {
                if ($itemS['id'] == '')
                {
                    $subprocess = new RiskMatrixSubProcess();
                    $subprocess->risk_matrix_id = $riskMatrix->id;
                }
                else
                    $subprocess = RiskMatrixSubProcess::find($itemS['id']);

                $subprocess->sub_process_id = $itemS['sub_process_id'];

                if(!$subprocess->save()){
                    return $this->respondHttp500();
                }

                foreach ($itemS['risks'] as $keyD => $itemR)
                {
                    if ($itemR['id'] == '')
                        $risk = new SubProcessRisk();
                    else
                        $risk = SubProcessRisk::find($itemR['id']);
                    
                    //TAGS
                    

                    //////////////////////////////
                    
                    $risk->rm_subprocess_id = $subprocess->id;
                    $risk->risk_id = $itemR['risk_id'];
                    $risk->nomenclature = $itemR['nomenclature'];
                    $risk->risk_sequence = $itemR['risk_sequence'];
                    $risk->economic = $itemR['economic'];
                    $risk->quality_care_patient_safety =  $itemR['quality_care_patient_safety'];
                    $risk->reputational = $itemR['reputational'];
                    $risk->legal_regulatory = $itemR['legal_regulatory'];
                    $risk->environmental = $itemR['environmental'];
                    $risk->max_inherent_impact = $itemR['max_inherent_impact'];
                    $risk->description_inherent_impact = $itemR['description_inherent_impact'];
                    $risk->max_inherent_frequency = $itemR['max_inherent_frequency'];            
                    $risk->description_inherent_frequency = $itemR['description_inherent_frequency'];
                    $risk->inherent_exposition = $itemR['inherent_exposition'];
                    $risk->controls_decrease = $itemR['controls_decrease'];
                    $risk->nature = $itemR['nature'];
                    $risk->evidence = $itemR['evidence'];
                    $risk->coverage = $itemR['coverage'];
                    $risk->documentation = $itemR['documentation'];
                    $risk->segregation = $itemR['segregation'];
                    $risk->control_evaluation = $itemR['control_evaluation'];
                    $risk->percentege_mitigation = $itemR['percentege_mitigation'];
                    $risk->max_residual_impact = $itemR['max_residual_impact'];
                    $risk->description_residual_impact = $itemR['description_residual_impact'];
                    $risk->max_residual_frequency = $itemR['max_residual_frequency'];
                    $risk->description_residual_frequency = $itemR['description_residual_frequency'];

                    $risk->residual_exposition = $itemR['residual_exposition'];
                    $risk->max_impact_event_risk = $itemR['max_impact_event_risk'];

                    if(!$risk->save()){
                        return $this->respondHttp500();
                    }

                    foreach ($itemR['causes_controls'] as $keyC => $itemC)
                    {
                        if ($itemC['id'] == '')
                            $cause = new Cause();
                        else
                            $cause = Cause::find($itemC['id']);

                        $cause->rm_subprocess_risk_id = $risk->id;
                        $cause->cause = $itemC['cause'];

                        if(!$cause->save()){
                            return $this->respondHttp500();
                        }

                        foreach ($itemC['controls'] as $keyC2 => $itemC2)
                        {
                            if ($itemC2['id'] == '')
                                $control = new CauseControl();
                            else
                                $control = CauseControl::find($itemC2['id']);

                            $controls = $this->tagsPrepare($itemC2['controls']);
                            $this->tagsSave($controls, TagsRmCauseControls::class);

                            $control->controls = $controls->implode(',');
                            $control->rm_cause_id = $cause->id;
                            $control->number_control = $itemC2['number_control'];
                            $control->nomenclature = $itemC2['nomenclature'];

                            if(!$control->save()){
                                return $this->respondHttp500();
                            }
                        }

                    }

                    foreach ($itemR['indicators'] as $keyI => $itemI)
                    {
                        if ($itemI['id'] == '')
                            $indicator = new Indicators();
                        else
                            $indicator = Indicators::find($itemI['id']);

                        $indicator->rm_subprocess_risk_id = $risk->id;
                        $indicator->indicator = $itemI['indicator'];

                        if(!$indicator->save()){
                            return $this->respondHttp500();
                        }
                    }

                    $subprocess_procedence = SubProcess::find($itemS['sub_process_id']);
                    $risk_procedence = Risk::find($risk->risk_id);

                    $detail_procedence = 'Mátriz de Riesgos - Subproceso: '. $subprocess_procedence->name . '. Riesgo: '. $risk_procedence->name;

                    /**Planes de acción*/
                    ActionPlan::
                          model($risk)
                        ->activities($itemR['actionPlan'])
                        ->detailProcedence($detail_procedence)
                        ->save();
                }

                if (isset($itemS['risksRemoved']) && COUNT($itemS['risksRemoved']) > 0)
                {
                    foreach ($itemS['risksRemoved'] as $key => $value)
                    {
                        $riskDel = SubProcessRisk::find($value['id']);

                        if ($riskDel)
                        {
                            ActionPlan::model($riskDel)->modelDeleteAll();
                            $riskDel->delete();
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

    public function getEvaluationControl()
    {
        $data = $this->getControlEvaluation();

        return $data;
    }

    public function getImpacts()
    {
        $data = $this->getDescriptionImpactsFrequency();

        return $data;
    }

    public function getMitigation()
    {
        $data = $this->percentageMitigation();
        
        return $data;
    }

    public function getAbrevRegional(Request $request)
    {
        $abrev = EmployeeRegional::find($request->id);

        if ($abrev->abbreviation)
            $nom_reg = $abrev->abbreviation;
        else
            $nom_reg = '';

        return $this->respondHttp200([
            'data' => $nom_reg,
        ]);
    }

    public function getAbrevMacro(Request $request)
    {
        $abrev = TagsProcess::find($request->id);

        if ($abrev->abbreviation)
            $nom_reg = $abrev->abbreviation;
        else
            $nom_reg = '';

        return $this->respondHttp200([
            'data' => $nom_reg,
        ]);
    }

    public function getAbrevHeadquarter(Request $request)
    {
        $abrev = EmployeeHeadquarter::find($request->id);

        if ($abrev->abbreviation)
            $nom_reg = $abrev->abbreviation;
        else
            $nom_reg = '';

        return $this->respondHttp200([
            'data' => $nom_reg,
        ]);
    }

    public function getAbrevProcess(Request $request)
    {
        $abrev = EmployeeProcess::find($request->id);

        if ($abrev->abbreviation)
            $nom_reg = $abrev->abbreviation;
        else
            $nom_reg = '';

        return $this->respondHttp200([
            'data' => $nom_reg,
        ]);
    }

    public function getAbrevArea(Request $request)
    {
        $abrev = EmployeeArea::find($request->id);

        if ($abrev->abbreviation)
            $nom_reg = $abrev->abbreviation;
        else
            $nom_reg = '';

        return $this->respondHttp200([
            'data' => $nom_reg,
        ]);
    }
}
