<?php

namespace App\Http\Controllers\Api;

use Storage;
use Illuminate\Http\Request;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;
use App\Models\IndustrialSecure\DangerousConditions\Reports\ConditionType;
use App\Http\Requests\Api\ReportRequest;
use App\Http\Requests\IndustrialSecure\DangerousConditions\Reports\SaveQualificationRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\LocationFormTrait;
use File;
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
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
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
          $data = $request->all();

          $report = new Report();
          $report->condition_id = $request->condition_id;           
          $report->other_condition = $request->other_condition;
          $report->rate = $request->rate;
          $report->observation = $request->observation;
          $report->user_id = $this->user->id;
          $report->company_id = $request->company_id;
          
          if (!$report->save())
              return $this->respondHttp500();

          if ($this->updateModelLocationForm($report, $request->get('locations')))
              return $this->respondHttp500();

            \Log::info($request);

          ActionPlan::
                user($this->user)
            ->module('dangerousConditions')
            ->url(url('/administrative/actionplans'))
            ->model($report)
            ->activities($request->actionPlan)
            ->save();

            $data['actionPlan'] = ActionPlan::getActivities();

            ActionPlan::sendMail();
              
          DB::commit();

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
}
