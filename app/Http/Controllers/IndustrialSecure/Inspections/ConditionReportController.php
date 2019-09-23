<?php

namespace App\Http\Controllers\IndustrialSecure\Inspections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\IndustrialSecure\Inspections\ConditionReport;
use App\Models\IndustrialSecure\Inspections\Condition;
use App\Http\Requests\IndustrialSecure\Inspections\ConditionReportRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Carbon\Carbon;
use Session;
use DB;
use Validator;
use PDF;

class ConditionReportController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:inspect_conditionsReports_r');
        $this->middleware('permission:inspect_conditionsReports_u', ['only' => 'update']);
        $this->middleware('inspect_conditionsReports_d', ['only' => 'destroy']);
        //$this->middleware('permission:reinc_checks_export', ['only' => 'export']);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {              
        $reports = ConditionReport::select(
                    'sau_inspect_conditions_reports.*',
                    'sau_employees_regionals.name as regional',
                    'sau_employees_headquarters.name AS headquarter',
                    'sau_employees_processes.name as process',
                    'sau_users.name as user_name',
                    'sau_inspect_conditions.description as condition',
                    'sau_inspect_conditions_types.description as type',
                    'sau_inspect_conditions_reports.rate',
                    'sau_inspect_conditions_reports.created_at',
                )
                //->leftJoin('ph_activities','ph_activities.report_id', '=', 'ph_reports.id')
            ->join('sau_users', 'sau_users.id', 'sau_inspect_conditions_reports.user_id')
            ->join('sau_inspect_conditions', 'sau_inspect_conditions.id', 'sau_inspect_conditions_reports.condition_id')
            ->join('sau_inspect_conditions_types', 'sau_inspect_conditions_types.id', 'sau_inspect_conditions.condition_type_id')
            ->join('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_inspect_conditions_reports.headquarter_id')
            ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_inspect_conditions_reports.process_id')
            ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_inspect_conditions_reports.regional_id')
            ;

        return Vuetable::of($reports)
                    /*->editColumn('condition', function($column){
                        return strlen($column->condition) > 25 ? mb_substr($column->condition, 0, 24) . '...' : $column->condition;
                    })*/
                    ->make();
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
            $report = ConditionReport::findOrFail($id);

            $report->multiselect_regional = $report->regional ? $report->regional->multiselect() : []; 
            $report->multiselect_sede = $report->headquarter ? $report->headquarter->multiselect() : []; 
            $report->multiselect_proceso = $report->process ? $report->process->multiselect() : []; 
            $report->multiselect_area = $report->area ? $report->area->multiselect() : [];
            $report->multiselect_condition = $report->condition ? $report->condition->multiselect() : [];
            $report->user_name = $report->user->name;
            $report->old_1 = $report->image_1;
            $report->path_1 = Storage::disk('public')->url('inspections/images/'. $report->image_1);
            $report->old_2 = $report->image_2;
            $report->path_2 = Storage::disk('public')->url('inspections/images/'. $report->image_2);
            $report->old_3 = $report->image_3;
            $report->path_3 = Storage::disk('public')->url('inspections/images/'. $report->image_3);
        
            $report->actionPlan = ActionPlan::model($report)->prepareDataComponent();
                
            return $this->respondHttp200([
                'data' => $report,
            ]);
            
        }catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConditionReportRequest $request, ConditionReport $conditionsReport)
    {   
        DB::beginTransaction();
        try
        {
            $conditionsReport->fill($request->all());

            if(!$conditionsReport->update()){
                return $this->respondHttp500();
            }
            $actionPlan=$request->actionPlan;
            
            ActionPlan::
                    user(Auth::user())
                ->module('inspections')
                ->url(url('/administrative/actionplans'))
                ->model($conditionsReport)
                ->activities($actionPlan)
                ->save()
                ->sendMail();
        

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se actualizo el reporte'
            ]);
        }
        catch(\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  ConditionReport  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConditionReport $conditionsReport)
    {
        try
        {
        
        $report=$conditionsReport;
        $conditionsReport->delete();

        if($report->image_1){
            Storage::disk('public')->delete('inspections/images/'. $report->image_1);
        };

        if($report->image_2){
            Storage::disk('public')->delete('inspections/images/'. $report->image_2);
        };

        if($report->image_3){
            Storage::disk('public')->delete('inspections/images/'. $report->image_3);
        };
        } 
        catch(Exception $e) {
            
        return $this->respondHttp500();
      }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el reporte'
        ]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configuration\ConfigurationRequest $request
     * @return \Illuminate\Http\Response
     *image/jpg, image/jpeg,
     */
    public function storeImage(Request $request)
    {
        Validator::make($request->all(), [
            "image" => [
                function ($attribute, $value, $fail)
                {
                    if (($value && !is_string($value) && $value->getClientMimeType() != 'image/png') && 
                        ($value && !is_string($value) && $value->getClientMimeType() != 'image/jpg') &&
                        ($value && !is_string($value) && $value->getClientMimeType() != 'image/jpeg'))
                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        $report = ConditionReport::find($request->report_id);
        $data = $request->all();
        $picture = $request->column;

        if ($request->image != $report->$picture)
        {
            
            if ($request->image)
            {
                $file = $request->image;
                Storage::disk('public')->delete('inspections/images/'. $report->$picture);
                $nameFile = base64_encode(Auth::user()->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs('inspections/images/', $nameFile, 'public');
                $report->$picture = $nameFile;
                $data['image'] = $nameFile;
                $data['old'] = $nameFile;
                $data['path'] = Storage::disk('public')->url('inspections/images/'. $nameFile);
            }
            else
            {
                Storage::disk('public')->delete('inspections/images/'. $report->$picture);
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

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselectConditions(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $conditions = Condition::select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($conditions)
            ]);
        }
        else
        {
            $conditions = Condition::selectRaw("
                sau_inspect_conditions.id as id,
                sau_inspect_conditions.description as description
            ")->pluck('id', 'description');
        
            return $this->multiSelectFormat($conditions);
        }
    }

    public function download(ConditionReport $conditionsReport)
    {
      return Storage::disk('public')->download('inspections/images/'. $conditionsReport->image);
    }
}