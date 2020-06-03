<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;
use App\Models\IndustrialSecure\DangerousConditions\Reports\ConditionType;
use App\Models\General\Company;
use App\Http\Requests\Api\ReportRequest;
use App\Http\Requests\Api\ImageReportRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\LocationFormTrait;
use App\Http\Requests\Api\CompanyRequiredRequest;
use File;
use Carbon\Carbon;
use DB;

class ReportsController extends ApiController
{
  use LocationFormTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Stores the images of a given report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveImage(ImageReportRequest $request)
    {
        $report = Report::query();
        $report->company_scope = $request->company_id;
        $report = $report->findOrFail($request->report_id);

        $images = collect([]);

        $this->checkImgRequest($images, $request, 1);
        $this->checkImgRequest($images, $request, 2);
        $this->checkImgRequest($images, $request, 3);

        $i = 1;
        $replace = false;

        while ($images->count() > 0)
        {
          $column = "image_".$i;

          if (!$report->$column || $replace)
          {
            $this->checkImgStore($report, $images->pop(), $column);
          }

          if ($i == 3)
          {
            $i = 1;
            $replace = true;
          }
          else
            $i++;
        }

        return $this->respondHttp200([
            'data' => $report
        ]);
    }

    public function checkImgStore($report, $image, $column)
    {
      $fileName = md5($image->getClientOriginalName() . Carbon::now()) . $column . '.' . $image->getClientOriginalExtension();
      $file = file_get_contents($image->getRealPath());

      $report->img_delete($column);
      $report->store_image($column, $fileName, $file);
    }

    public function checkImgRequest(&$images, $request, $key)
    {
      $index = "image_".$key;

      if ($request->has($index) && $request->$index)
      {
        $images->push($request->$index);
      }
    }

    /**
     * Create the report to store the images later
     * @param  PreReportRequest $request
     * @return json
     */
    public function preReport(ReportRequest $request)
    {
        /*if (!$this->user->hasPermission('create_reports')) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }*/

      DB::beginTransaction();
        
      try
      {
        if ($request->id)
        {
          $report = Report::query();
          $report->company_scope = $request->company_id;
          $report = $report->findOrFail($request->id);
        }
        else
          $report = new Report();

        $report->fill($request->all());
        $report->user_id = $this->user->id;

        if (!$report->save())
            return $this->respondHttp500();

        ActionPlan::
              user($this->user)
          ->module('dangerousConditions')
          ->url(url('/administrative/actionplans'))
          ->model($report)
          ->activities($request->actionPlan)
          ->company($request->company_id)
          ->save()
          ->sendMail();
            
        DB::commit();

        $report->actionPlan = ActionPlan::getActivities();

      } catch (\Exception $e) {
          \Log::info($e->getMessage());
          DB::rollback();
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'data' => $report
      ]);
    }

    /**
     * Display the reports the user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listReportsUser()
    {
      try{
      $reports = Report::where('user_id', $this->user->id)->orderBy('created_at','desc')->paginate(15);
      $data = [];
      foreach ($reports as $key => $value) {
          
          $action_plans = [];

          foreach ($value->activities as $key => $item) {
            $activity = [
              'id' => $item->id,
              'description' => $item->description,
                'responsible' => $item->responsible()->pluck('name')->first(),
                'exec_date' => $item->exec_date != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->exec_date)->toDateString() : '',
                'watch_date' => $item->watch_date != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->watch_date)->toDateString() : '',
                'state' => $item->state
            ];
            array_push($action_plans, $activity);
          }

            array_push($data, [
              'id' => $value->id,
              'observation' => $value->observation,
              'image_1' => $value->image_1,
              'image_2' => $value->image_2,
              'image_3' => $value->image_3,
              'rate' => $value->rate,
              'condition' => [
                'condition' => $value->condition->description,
                'condition_type' => $value->condition->conditionType->description
              ],
              'location'=> $value->location->name,
              'area'=> $value->area->name,
              'created_at'=> $value->created_at != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->toDateString() : '',
              'action_plan' => $action_plans
            ]);
        }

        


        return response(json_encode([
          'response' => 'ok',
          'total' =>$reports->total(),
          'per_page' =>$reports->perPage(),
          'current_page' =>$reports->currentPage(),
          'last_page' =>$reports->lastPage(),
          'next_page_url' =>$reports->nextPageUrl(),
          'prev_page_url' =>$reports->previousPageUrl(),
          'from' =>$reports->firstItem(),
          'to' =>$reports->lastItem(),
          'data' => $data
        ],JSON_UNESCAPED_UNICODE), 200);
        
      }catch (\Exception $e) {
        \Log::error($e);
        return response(json_encode([
            'response' => 'error',
            'data' => 'Error inesperado, intentelo mas tarde.',
        ], JSON_UNESCAPED_UNICODE), 500);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->responderError('No encontrado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->responderError('No encontrado');
    }

    public function getIncentives(CompanyRequiredRequest $request)
    {
      try
      {
        $company = Company::find($request->company_id);
        
        if (!$company->ph_file_incentives || !$company->ph_state_incentives)
          return $this->respondHttp200();

        $headers = array(
          'Content-Type: application/pdf',
        );

        return Storage::disk('local')->download('file_incentives/'.$company->ph_file_incentives, 'Incentivos.pdf', $headers);
      }
      catch(\Exception $e){
        \Log::info($e->getMessage());
        return $this->respondHttp500();
      }
    }

    public function info()
    {
        $types = ConditionType::all();
        $result = [];

        foreach ($types as $type) {
            $result[] = [
                'description' => $type->description,
                'values' => $type->conditions,
            ];
        }

        return $this->respondHttp200([
          'data' => $result
      ]);
    }
}
