<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\Check\Facades\CheckManager;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements\CheckRequest;
use App\Jobs\PreventiveOccupationalMedicine\Reinstatements\CheckExportJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ConfigurableFormTrait;
use Carbon\Carbon;
use Session;
use DB;
use PDF;

class CheckController extends Controller
{ 
    use ConfigurableFormTrait;

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
        $this->middleware('permission:reinc_checks_export', ['only' => 'export']);
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
                    'sau_reinc_checks.deadline AS deadline',
                    'sau_reinc_checks.next_date_tracking AS next_date_tracking',
                    'sau_reinc_cie10_codes.code AS code',
                    'sau_reinc_checks.disease_origin AS disease_origin',
                    'sau_employees_regionals.name AS regional',
                    'sau_reinc_checks.state AS state',
                    'sau_employees.identification AS identification',
                    'sau_employees.name AS name'
                )
                ->join('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
                ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
                ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id');

        if ($request->current_check_id)
            $checks->where('sau_reinc_checks.id', '<>', $request->current_check_id);

        if ($request->employee_id)
            $checks->where('sau_reinc_checks.employee_id', '=', $request->employee_id);

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $checks->inIdentifications($this->getValuesForMultiselect($filters["identifications"]), $filters['filtersType']['identifications']);
            $checks->inNames($this->getValuesForMultiselect($filters["names"]), $filters['filtersType']['names']);
            $checks->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);
            $checks->inBusinesses($this->getValuesForMultiselect($filters["businesses"]), $filters['filtersType']['businesses']);
            $checks->inDiseaseOrigin($this->getValuesForMultiselect($filters["diseaseOrigin"]), $filters['filtersType']['diseaseOrigin']);
            $checks->inYears($this->getValuesForMultiselect($filters["years"]), $filters['filtersType']['years']);

            if (isset($filters["nextFollowDays"]))
                $checks->inNextFollowDays($this->getValuesForMultiselect($filters["nextFollowDays"]), $filters['filtersType']['nextFollowDays']);

            if (isset($filters["sveAssociateds"]))
                $checks->inSveAssociateds($this->getValuesForMultiselect($filters["sveAssociateds"]), $filters['filtersType']['sveAssociateds']);

            if (isset($filters["medicalCertificates"]))
                $checks->inMedicalCertificates($this->getValuesForMultiselect($filters["medicalCertificates"]), $filters['filtersType']['medicalCertificates']);

            if (isset($filters["relocatedTypes"]))
                $checks->inRelocatedTypes($this->getValuesForMultiselect($filters["relocatedTypes"]), $filters['filtersType']['relocatedTypes']);
                
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $checks->betweenDate($dates);
        }

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

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing', $check, $request->new_tracing, Auth::user()))
            {
                $check->delete();
                return $this->respondHttp500();
            }

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborNotes', $check, $request->new_labor_notes, Auth::user()))
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
                
            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\Tracing', $check, $request->new_tracing, Auth::user(), $request->oldTracings))
                return $this->respondHttp500();

            if (!CheckManager::saveTracing('App\Models\PreventiveOccupationalMedicine\Reinstatements\LaborNotes', $check, $request->new_labor_notes, Auth::user(), $request->oldLaborNotes))
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
            },
            'laborNotes' => function ($query) {
                $query->with('madeBy')->orderBy('created_at', 'desc');
            }
        ]);

        $check->start_recommendations = $this->formatDateToForm($check->start_recommendations);
        $check->end_recommendations = $this->formatDateToForm($check->end_recommendations);
        $check->monitoring_recommendations = $this->formatDateToForm($check->monitoring_recommendations);
        $check->process_origin_done_date = $this->formatDateToForm($check->process_origin_done_date);
        $check->process_pcl_done_date = $this->formatDateToForm($check->process_pcl_done_date);
        $check->date_controversy_origin_1 = $this->formatDateToForm($check->date_controversy_origin_1);
        $check->date_controversy_origin_2 = $this->formatDateToForm($check->date_controversy_origin_2);
        $check->date_controversy_pcl_1 = $this->formatDateToForm($check->date_controversy_pcl_1);
        $check->date_controversy_pcl_2 = $this->formatDateToForm($check->date_controversy_pcl_2);
        $check->relocated_date = $this->formatDateToForm($check->relocated_date);
        $check->start_restrictions = $this->formatDateToForm($check->start_restrictions);
        $check->end_restrictions = $this->formatDateToForm($check->end_restrictions);
        $check->incapacitated_last_extension = $this->formatDateToForm($check->incapacitated_last_extension);
        $check->deadline = $this->formatDateToForm($check->deadline);
        $check->next_date_tracking = $this->formatDateToForm($check->next_date_tracking);

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
            $medicalMonitoring->set_at = $this->formatDateToForm($medicalMonitoring->set_at);
            return $medicalMonitoring;
        });

        $check->laborMonitorings->transform(function($laborMonitoring, $index) {
            $laborMonitoring->set_at = $this->formatDateToForm($laborMonitoring->set_at);
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

        $oldLaborNotes = [];

        foreach ($check->laborNotes as $tracing)
        {
            array_push($oldLaborNotes, [
                'id' => $tracing->id,
                'description' => $tracing->description,
                'made_by' => $tracing->madeBy->name,
                'updated_at' => $tracing->updated_at->toDateString()
            ]);
        }

        $check->oldLaborNotes = $oldLaborNotes;

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

    public function generateTracing(Request $request)
    {
        $check = Check::selectRaw(
           'sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_employees_positions.name AS position,
            sau_reinc_cie10_codes.description AS diagnosis,
            sau_reinc_checks.disease_origin AS disease_origin,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            sau_reinc_checks.end_recommendations AS end_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_reinc_checks.monitoring_recommendations AS monitoring_recommendations,
            sau_employees_headquarters.name AS headquarter'
        )
        ->join('sau_employees', 'sau_employees.id', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_reinc_cie10_codes', 'sau_reinc_cie10_codes.id', 'sau_reinc_checks.cie10_code_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $request->check_id)
        ->first();

        $company = Company::select('logo')->where('id', Session::get('company_id'))->first();
        $new_date_tracing = $request->new_date_tracing ? (Carbon::createFromFormat('D M d Y', $request->new_date_tracing))->format('Y-m-d') : '';

        $data = [
            'has_tracing' => $request->has_tracing,
            'new_date_tracing' => $new_date_tracing,
            'tracing_description' => $request->tracing_description,
            'check' => $check,
            'logo' => ($company && $company->logo) ? $company->logo : null
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        
        $pdf = PDF::loadView('pdf.tracing', $data);

        return $pdf->stream('seguimiento.pdf');
    }

    public function generateLetter(Request $request)
    {
        $check = Check::selectRaw(
           'sau_reinc_checks.detail as check_detail,
            sau_reinc_checks.start_recommendations AS start_recommendations,
            DATEDIFF(sau_reinc_checks.end_recommendations, sau_reinc_checks.start_recommendations) AS time_different,
            sau_reinc_checks.indefinite_recommendations AS indefinite_recommendations,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name AS headquarter,
            sau_employees.name AS name,
            sau_employees.identification AS identification,
            sau_reinc_checks.origin_recommendations as origin_recommendations,
            sau_employees_positions.name AS position'
        )
        ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', '=', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', '=', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_positions', 'sau_employees_positions.id', '=', 'sau_employees.employee_position_id')
        ->where('sau_reinc_checks.id', $request->check_id)
        ->first();
            
        $company = Company::select('logo')->where('id', Session::get('company_id'))->first();

        $data = [
            'to' => $request->to,
            'from' => $request->from,
            'subject' => $request->subject, 
            'user' => Auth::user()->name,
            'check' => $check,
            'firm' => $request->firm,
            'recommendations' => $this->replaceLast(',', ' y ', $request->selectedRecommendations),
            'logo' => ($company && $company->logo) ? $company->logo : null
        ];

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $formModel = $this->getFormModel('reinc_letter_recomendatios');

        if ($formModel == 'default')
        { 
            $pdf = PDF::loadView('pdf.letter', $data);
        }
        else if ($formModel == 'vivaAir')
        {
            $pdf = PDF::loadView('pdf.letterVivaAir', $data);
        }
        else if($formModel == 'reditos')
        {
            $pdf = PDF::loadView('pdf.letterReditos', $data);
        }

        return $pdf->stream('recomendaciones.pdf');
    
    }

    private function replaceLast($buscar, $remplazar, $texto)
    {
        $pos = strrpos($texto, $buscar);
        
        if($pos !== false)
            $texto = substr_replace($texto, $remplazar, $pos, strlen($buscar));
        
        return $texto;
    }

    public function multiselectDiseaseOrigin(Request $request)
    {
        return $this->getSelectOptions("reinc_select_disease_origin");
    }

    public function multiselectSveAssociateds(Request $request)
    {
        return $this->getSelectOptions("reinc_select_sve_associated");
    }

    public function multiselectMedicalCertificates(Request $request)
    {
        return $this->getSelectOptions("reinc_select_medical_certificate_ueac");
    }

    public function multiselectRelocatedTypes(Request $request)
    {
        return $this->getSelectOptions("reinc_select_relocated_types");
    }

    /**
     * returns the days remaining for the next follow-up of the reports
     * @return collection
     */
    public function multiselectNextFollowDays(Request $request)
    {
        $checks = Check::selectRaw('next_date_tracking, DATEDIFF(next_date_tracking, CURDATE()) as day')
                    ->join('sau_employees', 'sau_employees.id', '=', 'sau_reinc_checks.employee_id')
                    ->whereRaw('next_date_tracking >= CURDATE()')
                    ->groupBy('next_date_tracking')
                    ->orderBy('day');
        
        $data = $checks->pluck('next_date_tracking', 'day')->toArray();
        $data['Vencidas'] = 'Vencidas';
        $data['No Aplica'] = 'No Aplica';

        return $this->multiSelectFormat(collect($data));
    }

    /**
     * returns the days remaining for the next follow-up of the reports
     * @return collection
     */
    public function multiselectYears()
    {
        $checks = Check::selectRaw(
            'DISTINCT YEAR(sau_reinc_checks.created_at) AS year'
        )
        ->orderBy('year')
        ->get()
        ->pluck('year', 'year');

      return $this->multiSelectFormat($checks);
    }

    /**
     * toggles the check state between open and close
     * @param  Check  $check
     * @return \Illuminate\Http\Response
     */
    public function toggleState(Request $request, Check $check)
    {
        $newState = $check->isOpen() ? "CERRADO" : "ABIERTO";
        $data = ['state' => $newState];

        if ($request->has('deadline') && $check->isOpen())
            $data['deadline'] = $this->formatDateToSave($request->get('deadline'));
        else 
            $data['deadline'] = null;

        if (!$check->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del reporte'
        ]);
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
      try
      {
        $identifications = $this->getValuesForMultiselect($request->identifications);
        $names = $this->getValuesForMultiselect($request->names);
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $businesses = $this->getValuesForMultiselect($request->businesses);
        $diseaseOrigin = $this->getValuesForMultiselect($request->diseaseOrigin);
        $years = $this->getValuesForMultiselect($request->years);
        $nextFollowDays = $request->has('nextFollowDays') ? $this->getValuesForMultiselect($request->nextFollowDays) : null;
        $sveAssociateds = $request->has('sveAssociateds') ? $this->getValuesForMultiselect($request->sveAssociateds) : null;
        $medicalCertificates = $request->has('medicalCertificates') ? $this->getValuesForMultiselect($request->medicalCertificates) : null;
        $relocatedTypes = $request->has('relocatedTypes') ? $this->getValuesForMultiselect($request->relocatedTypes) : null;
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }

        $filters = [
            'identifications' => $identifications,
            'names' => $names,
            'regionals' => $regionals,
            'businesses' => $businesses,
            'diseaseOrigin' => $diseaseOrigin,
            'years' => $years,
            'nextFollowDays' => $nextFollowDays,
            'sveAssociateds' => $sveAssociateds,
            'medicalCertificates' => $medicalCertificates,
            'relocatedTypes' => $relocatedTypes,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        CheckExportJob::dispatch(Auth::user(), Session::get('company_id'), $filters);
      
        return $this->respondHttp200();
      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }
}
