<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Storage;
use App\Models\General\Module;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;
use App\Models\IndustrialSecure\DangerousConditions\Reports\ConditionType;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Reports\ReportRequest;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Reports\SaveQualificationRequest;
use App\Jobs\IndustrialSecure\DangerousConditions\Reports\ReportExportJob;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Validator;
use App\Traits\Filtertrait;
use DB;

class ReportController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:ph_reports_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:ph_reports_r, {$this->team}");
        $this->middleware("permission:ph_reports_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:ph_reports_d, {$this->team}", ['only' => 'destroy']);
        $this->middleware("permission:ph_reports_export, {$this->team}", ['only' => 'export']);
        $this->middleware("permission:ph_reports_qualifications, {$this->team}", ['only' => ['saveImage', 'downloadImage', 'saveQualification']]);
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
        $reports = Report::select(
            'sau_ph_reports.id',
            'sau_employees_regionals.name as regional',
            'sau_employees_headquarters.name AS headquarter',
            'sau_employees_processes.name AS process',
            'sau_employees_areas.name AS area',
            'sau_ph_reports.created_at',
            'sau_users.name as user',
            'sau_ph_conditions.description as condition',
            'sau_ph_conditions_types.description as type',
            'sau_ph_reports.rate',
            'sau_ph_reports.state'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ph_reports.user_id')
        ->join('sau_ph_conditions', 'sau_ph_conditions.id', 'sau_ph_reports.condition_id')
        ->join('sau_ph_conditions_types', 'sau_ph_conditions_types.id', 'sau_ph_conditions.condition_type_id')
        ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_reports.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_reports.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_reports.employee_area_id')
        ->orderBy('sau_ph_reports.id', 'DESC');

        $url = "/industrialsecure/reports";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $reports->inConditions($this->getValuesForMultiselect($filters["conditions"]), $filters['filtersType']['conditions']);
            $reports->inConditionTypes($this->getValuesForMultiselect($filters["conditionTypes"]), $filters['filtersType']['conditionTypes']);
            $reports->betweenDate($this->formatDatetimeToBetweenFilter($filters["dateRange"]));

            $states = $this->getValuesForMultiselect($filters["states"]);

            if (COUNT($states) > 0)
            {
                $module_id = Module::where('name', 'dangerousConditions')->first()->id;

                $reports
                ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.item_id', 'sau_ph_reports.id')
                ->join('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
                ->where('item_table_name', 'sau_ph_reports')
                ->where('sau_action_plans_activity_module.module_id', $module_id)
                ->inStates($states, $filters['filtersType']['states'])
                ->groupBy('sau_ph_reports.id', 'regional', 'sau_ph_reports.created_at', 'user', 'condition', 'type', 'sau_ph_reports.rate');
            }
        }

        return Vuetable::of($reports)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReportRequest $request)
    {
        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();

        DB::beginTransaction();

        try
        {
            $report = new Report($request->except(['image_1', 'image_2', 'image_3']));
            $report->company_id = $this->company;
            $report->user_id = $this->user->id;

            if ($request->image_1)
            {
                $file1 = $request->image_1;
                $nameFile1 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file1->getClientOriginalExtension();
                $file1->storeAs($report->path_base(), $nameFile1, 's3_DConditions');
                $report->image_1 = $nameFile1;
            }

            if ($request->image_2)
            {
                $file2 = $request->image_2;
                $nameFile2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file2->getClientOriginalExtension();
                $file2->storeAs($report->path_base(), $nameFile2, 's3_DConditions');
                $report->image_2 = $nameFile2;
            }

            if ($request->image_3)
            {
                $file3 = $request->image_3;
                $nameFile3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file3->getClientOriginalExtension();
                $file3->storeAs($report->path_base(), $nameFile3, 's3_DConditions');
                $report->image_3 = $nameFile3;
            }
            
            if (!$report->save())
                return $this->respondHttp500();

            if ($this->updateModelLocationForm($report, $request->get('locations')))
                return $this->respondHttp500();

            $details = $report->condition->conditionType->description. ': ' . $report->condition->description;

            if ($confLocation['regional'] == 'SI')
                $detail_procedence = 'Inspecciones no planesdas. '. $details . '- ' . $keywords['regional']. ': ' .  $report->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $detail_procedence = $detail_procedence . '- ' .$keywords['headquarter']. ': ' .  $report->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $detail_procedence = $detail_procedence . '- ' .$keywords['process']. ': ' .  $report->process->name;
            if ($confLocation['area'] == 'SI')
                $detail_procedence = $detail_procedence . '- ' .$keywords['area']. ': ' .  $report->area->name;

            ActionPlan::
                    user($this->user)
                ->module('dangerousConditions')
                ->url(url('/administrative/actionplans'))
                ->model($report)
                ->regional($report->regional ? $report->regional->name : null)
                ->headquarter($report->headquarter ? $report->headquarter->name : null)
                ->area($report->area ? $report->area->name : null)
                ->process($report->process ? $report->process->name : null)
                ->details($details)
                ->detailProcedence($detail_procedence)
                ->activities($request->actionPlan)
                ->save();

            ActionPlan::sendMail();

            $this->saveLogActivitySystem('Inspecciones - Inspecciones no planeadas', 'Se creo la inspección no planeada realizada '.$detail_procedence);
                
            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el reporte'
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
            $report = Report::withoutGlobalScopes()->findOrFail($id);

            $report->user;
            $report->multiselect_condition = $report->condition ? $report->condition->multiselect() : [];
            $report->old_1 = $report->image_1;
            $report->path_1 = $report->path_image('image_1');
            $report->old_2 = $report->image_2;
            $report->path_2 = $report->path_image('image_2');
            $report->old_3 = $report->image_3;
            $report->path_3 = $report->path_image('image_3');
            $report->actionPlan = ActionPlan::model($report)->prepareDataComponent();
            $report->locations = $this->prepareDataLocationForm($report);
            $report->type_condition = $report->condition->condition_type_id;

            return $this->respondHttp200([
                'data' => $report,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InspectionRequest  $request
     * @param  Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function update(ReportRequest $request, Report $report)
    {
        $keywords = $this->user->getKeywords();
        $confLocation = $this->getLocationFormConfModule();

        DB::beginTransaction();

        try
        {
            $report->fill($request->except(['image_1', 'image_2', 'image_3']));

            if ($request->image_1 != $report->image_1)
            {
                if ($request->image_1)
                {
                    $file = $request->image_1;
                    $nameFile1 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->getClientOriginalExtension();
                    $report->img_delete('image_1');
                    $file->storeAs($report->path_base(), $nameFile1, 's3_DConditions');
                }
                else
                {
                    $report->img_delete('image_1');
                    $report->image_1 = NULL;
                }
            }

            if ($request->image_2 != $report->image_2)
            {
                if ($request->image_2)
                {
                    $file2 = $request->image_2;
                    $nameFile2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file2->getClientOriginalExtension();                    
                    $report->img_delete('image_2');
                    $file2->storeAs($report->path_base(), $nameFile2, 's3_DConditions');
                    $report->image_2 = $nameFile2;
                }
                else
                {
                    $report->img_delete('image_2');
                    $report->image_2 = NULL;
                }
            }

            if ($request->image_3 != $report->image_3)
            {
                if ($request->image_3)
                {
                    $file3 = $request->image_3;
                    $nameFile3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file3->getClientOriginalExtension();
                    $report->img_delete('image_3');
                    $file3->storeAs($report->path_base(), $nameFile3, 's3_DConditions');
                    $report->image_3 = $nameFile3;
                }
                else
                {
                    $report->img_delete('image_3');
                    $report->image_2 = NULL;
                }
            }
            
            if (!$report->update())
                return $this->respondHttp500();

            if ($this->updateModelLocationForm($report, $request->get('locations')))
                return $this->respondHttp500();

            $details = $report->condition->conditionType->description. ': ' . $report->condition->description;

            if ($confLocation['regional'] == 'SI')
                $detail_procedence = 'Inspecciones - Inspecciones no planeadas. Hallazgo: '. $report->condition->description . ' - ' . $keywords['regional']. ': ' .  $report->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $report->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $report->process->name;
            if ($confLocation['area'] == 'SI')
                $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $report->area->name;

            ActionPlan::
                    user($this->user)
                ->module('dangerousConditions')
                ->url(url('/administrative/actionplans'))
                ->model($report)
                ->regional($report->regional ? $report->regional->name : null)
                ->headquarter($report->headquarter ? $report->headquarter->name : null)
                ->area($report->area ? $report->area->name : null)
                ->process($report->process ? $report->process->name : null)
                ->details($details)
                ->detailProcedence($detail_procedence)
                ->activities($request->actionPlan)
                ->save();

            ActionPlan::sendMail();

            $this->saveLogActivitySystem('Inspecciones - Inspecciones no planeadas', 'Se edito la inspección no planeada realizada '.$detail_procedence);

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el reporte'
        ]);
    }

    public function downloadImage($id, $column)
    {
        $report = Report::findOrFail($id);

        return $report->donwload_img($column);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Inspection $inspection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        DB::beginTransaction();

        try
        { 
            $keywords = $this->user->getKeywords();
            $confLocation = $this->getLocationFormConfModule();
            $description_delete = '';

            if ($confLocation['regional'] == 'SI')
                $description_delete = 'Hallazgo: '. $report->condition->description . ' - ' . $keywords['regional']. ': ' .  $report->regional->name;
            if ($confLocation['headquarter'] == 'SI')
                $description_delete = $description_delete . ' - ' .$keywords['headquarter']. ': ' .  $report->headquarter->name;
            if ($confLocation['process'] == 'SI')
                $description_delete = $description_delete . ' - ' .$keywords['process']. ': ' .  $report->process->name;
            if ($confLocation['area'] == 'SI')
                $description_delete = $description_delete . ' - ' .$keywords['area']. ': ' .  $report->area->name;

            ActionPlan::model($report)->modelDeleteAll();

            $this->saveLogDelete('Inspecciones - Inspecciones no planeadas', 'Se elimino la inspección no planeada realizada '.$description_delete);

            if (!$report->delete())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el reporte'
        ]);
    }

    public function export(Request $request)
    {
      try
      {
        $conditions = $this->getValuesForMultiselect($request->conditions);
        $conditionTypes = $this->getValuesForMultiselect($request->conditionTypes);
        $states = $this->getValuesForMultiselect($request->states);
        $dates = $this->formatDatetimeToBetweenFilter($request->dateRange);
        $filtersType = $request->filtersType;

        $filters = [
            'conditions' => $conditions,
            'conditionTypes' => $conditionTypes,
            'states' => $states,
            'dates' => $dates,
            'filtersType' => $filtersType
        ];

        ReportExportJob::dispatch($this->user, $this->company, $filters);
      
        return $this->respondHttp200();

      } catch(Exception $e) {
        return $this->respondHttp500();
      }
    }

    public function multiselectConditions(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $conditions = Condition::select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->orderBy('description');

            $condition_type = $request->get('condition');
                    
            if (is_numeric($condition_type))
                $conditions->where('condition_type_id', $condition_type);         
            
            $conditions = $conditions->orderBy('description')->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($conditions)
            ]);
        }
        else
        {
            $conditions = Condition::selectRaw("
                sau_ph_conditions.id as id,
                sau_ph_conditions.description as description
            ")
            ->orderBy('description')
            ->pluck('id', 'description');
        
            return $this->multiSelectFormat($conditions);
        }
    }

    public function multiselectConditionTypes(Request $request)
    {
        $conditions = ConditionType::selectRaw("
            sau_ph_conditions_types.id as id,
            sau_ph_conditions_types.description as description
        ")
        ->orderBy('description')->pluck('id', 'description');
    
        return $this->multiSelectFormat($conditions);
        
    }

    public function saveImage(Request $request)
    {
        Validator::make($request->all(), [
            "image" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        $report = Report::findOrFail($request->id);
        $data = $request->all();
        $picture = $request->column;

        if ($request->image != $report->$picture)
        {
            if ($request->image)
            {
                $file = $request->image;
                Storage::disk('public')->delete('industrialSecure/dangerousConditions/reports/images/'. $report->$picture);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->getClientOriginalExtension();
                $file->storeAs('industrialSecure/dangerousConditions/reports/images/', $nameFile, 'public');
                $report->$picture = $nameFile;
                $data['image'] = $nameFile;
                $data['old'] = $nameFile;
                $data['path'] = Storage::disk('public')->url('industrialSecure/dangerousConditions/reports/images/'. $nameFile);
            }
            else
            {
                Storage::disk('public')->delete('industrialSecure/dangerousConditions/reports/images/'. $report->$picture);
                $report->$picture = NULL;
                $data['image'] = "";
                $data['old'] = NULL;
                $data['path'] = NULL;
            }
        }

        if (!$report->update())
            return $this->respondHttp500();

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    /*public function saveQualification(SaveQualificationRequest $request)
    {
        try
        {
            DB::beginTransaction();

            $data = $request->all();

            $report = Report::findOrFail($request->id);

            $details = $report->condition->conditionType->description. ': ' . $report->condition->description;

            ActionPlan::
                    user($this->user)
                ->module('dangerousConditions')
                ->url(url('/administrative/actionplans'))
                ->model($report)
                ->regional($report->regional ? $report->regional->name : null)
                ->headquarter($report->headquarter ? $report->headquarter->name : null)
                ->area($report->area ? $report->area->name : null)
                ->process($report->process ? $report->process->name : null)
                ->details($details)
                ->activities($request->actionPlan)
                ->save();

            $data['actionPlan'] = ActionPlan::getActivities();

            ActionPlan::sendMail();

            DB::commit();

            return $this->respondHttp200([
                'data' => $data
            ]);

        } catch (Exception $e){
            //\Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }*/
}
