<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\Check\Facades\CheckManager;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Session;
use DB;

class CheckController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reinc_checks_c', ['only' => 'store']);
        $this->middleware('permission:reinc_checks_r');
        $this->middleware('permission:reinc_checks_u', ['only' => 'update']);
        $this->middleware('permission:reinc_checks_d', ['only' => 'destroy']);
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
        $checks = Check::select(
                    'sau_reinc_checks.id AS id',
                    'sau_reinc_cie10_codes.code AS code',
                    'sau_reinc_checks.disease_origin AS disease_origin',
                    'sau_employees_regionals.name AS regional',
                    'sau_reinc_checks.state AS state',
                    'sau_employees.name AS name'
                )
                ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id');

        return Vuetable::of($checks)
                    ->addColumn('reinstatements-checks-edit', function ($check) {

                        return $check->isOpen();
                    })
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckRequest $request)
    {
        $this->validate($request, CheckManager::getProcessRules($request));
        
        try
        {
            DB::beginTransaction();

            $check = new Check(CheckManager::checkNullAttrs($request, Session::get('company_id')));
            $check->company_id = Session::get('company_id');

            if (!$check->save())
                return $this->respondHttp500();

            if (!CheckManager::saveMedicalMonitoring($check, $request->medical_monitorings))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveLaborMonitoring($check, $request->labor_monitorings))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveTracing($check, $request->new_tracing, Auth::user()))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            DB::commit();

        } catch (Exception $e){
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el formulario'
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
            $check = Check::findOrFail($id);

            return $this->respondHttp200([
                'data' => $this->getCheckView($check)
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest  $request
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function update(CheckRequest $request, Check $check)
    {
        $this->validate($request, CheckManager::getProcessRules($request));
        
        try
        {
            DB::beginTransaction();

            $check->fill(CheckManager::checkNullAttrs($request, Session::get('company_id')));
            
            if (!$check->save())
                return $this->respondHttp500();

            if (!CheckManager::saveMedicalMonitoring($check, $request->medical_monitorings, true))
                return $this->respondHttp500();

            if (!CheckManager::saveLaborMonitoring($check, $request->labor_monitorings, true))
                return $this->respondHttp500();

            if (!CheckManager::saveTracing($check, $request->new_tracing, Auth::user(), $request->oldTracings))
                return $this->respondHttp500();

            DB::commit();

        } catch (Exception $e){
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el formulario'
        ]);       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function destroy(Check $check)
    {
        if (!$check->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino el formulario'
        ]);
    }

    /**
     * this method must be used ONLY for the show and edit method
     * of this class.
     *
     * this method builds all the parameters that an instance of check
     * must have in order to work the form in the view
     *
     * and the return the form view specifying the parameter "isShow"
     * according to the show or edit view
     * 
     * @param  boolean $isShow
     * @return \Illuminate\Http\Response
     */
    private function getCheckView(Check $check)
    {
        $authUser = Auth::user();

        $check->load([
            'employee',
            'cie10Code',
            'medicalMonitorings',
            'laborMonitorings',
            'tracings' => function ($query) use ($authUser) {
                
                /*if (!$authUser->checkRoleDefined('Superadmin') && $authUser->can('reinc_checks_view_last_tracing')) {
                    $query->with('madeBy')->orderBy('created_at', 'desc')->limit(1);
                }
                else {
                    $query->with('madeBy')->orderBy('created_at', 'desc');
                }*/
                
                $query->with('madeBy')->orderBy('created_at', 'desc');
            }
        ]);

        $check->start_recommendations = $this->formatDate($check->start_recommendations);
        $check->end_recommendations = $this->formatDate($check->end_recommendations);
        $check->monitoring_recommendations = $this->formatDate($check->monitoring_recommendations);
        $check->process_origin_done_date = $this->formatDate($check->process_origin_done_date);
        $check->process_pcl_done_date = $this->formatDate($check->process_pcl_done_date);
        $check->date_controversy_origin_1 = $this->formatDate($check->date_controversy_origin_1);
        $check->date_controversy_origin_2 = $this->formatDate($check->date_controversy_origin_2);
        $check->date_controversy_pcl_1 = $this->formatDate($check->date_controversy_pcl_1);
        $check->date_controversy_pcl_2 = $this->formatDate($check->date_controversy_pcl_2);
        $check->relocated_date = $this->formatDate($check->relocated_date);
        $check->start_restrictions = $this->formatDate($check->start_restrictions);
        $check->end_restrictions = $this->formatDate($check->end_restrictions);
        $check->incapacitated_last_extension = $this->formatDate($check->incapacitated_last_extension);
        $check->deadline = $this->formatDate($check->deadline);
        $check->next_date_tracking = $this->formatDate($check->next_date_tracking);

        $check->old_process_origin_file = $check->process_origin_file;
        $check->old_process_pcl_file = $check->process_pcl_file;

        $check->multiselect_employee = $check->employee->multiselect();
        $check->multiselect_cie10Code = $check->cie10Code->multiselect();
        $check->multiselect_restriction = $check->restriction ? $check->restriction->multiselect() : [];
        $check->relocated_regional_multiselect = $check->relocatedRegional ? $check->relocatedRegional->multiselect() : [];
        $check->relocated_headquarter_multiselect = $check->relocatedHeadquarter ? $check->relocatedHeadquarter->multiselect() : [];
        $check->relocated_process_multiselect = $check->relocatedProcess ? $check->relocatedProcess->multiselect() : [];
        $check->relocated_position_multiselect = $check->relocatedPosition ? $check->relocatedPosition->multiselect() : [];

        $check->medicalMonitorings->transform(function($medicalMonitoring, $index) {
            $medicalMonitoring->set_at = $this->formatDate($medicalMonitoring->set_at);
            return $medicalMonitoring;
        });

        $check->laborMonitorings->transform(function($laborMonitoring, $index) {
            $laborMonitoring->set_at = $this->formatDate($laborMonitoring->set_at);
            return $laborMonitoring;
        });

        $check->new_tracing = '';

        $oldTracings = [];

        foreach ($check->tracings as $tracing)
        {
            array_push($oldTracings, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy->name,
                'updated_at' => $tracing->updated_at->toDateString()
            ]);
        }

        $check->oldTracings = $oldTracings;

        return $check;
    }

    public function downloadOriginFile(Check $check)
    {
      return Storage::disk('public')->download('preventiveOccupationalMedicine/reinstatements/files/'.Session::get('company_id').'/'.$check->process_origin_file);
    }

    public function downloadPclFile(Check $check)
    {
      return Storage::disk('public')->download('preventiveOccupationalMedicine/reinstatements/files/'.Session::get('company_id').'/'.$check->process_pcl_file);
    }

    private function formatDate($date)
    {
        if ($date)
            $date = (Carbon::createFromFormat('Y-m-d', $date))->format('D M d Y');

        return $date;
    }
}
