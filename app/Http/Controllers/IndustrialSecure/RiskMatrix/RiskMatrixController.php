<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Http\Requests\IndustrialSecure\RiskMatrix\RiskMatrixRequest;
use App\Models\IndustrialSecure\RiskMatrix\TagsRmParticipant;
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
                $msg = 'Se actualizo la matriz de peligro';

                /*$history_change = $this->tagsPrepare($request->get('changeHistory'));
                $this->tagsSave($history_change, TagsHistoryChange::class);

                $riskMatrix->histories()->create([
                    'user_id' => $this->user->id,
                    'description' => $history_change->implode(',')
                ]);*/
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
            
            if(!$riskMatrix->save()){
                return $this->respondHttp500();
            }

            if($this->updateModelLocationForm($riskMatrix, $request->get('locations')))
            {
                return $this->respondHttp500();
            }

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
}
