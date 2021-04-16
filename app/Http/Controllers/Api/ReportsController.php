<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;
use App\Models\IndustrialSecure\DangerousConditions\Reports\ConditionType;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\General\Company;
use App\Http\Requests\Api\ReportRequest;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Http\Requests\Api\ImageReportRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\LocationFormTrait;
use App\Http\Requests\Api\CompanyRequiredRequest;
use File;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;

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
    /* public function saveImage(ImageReportRequest $request)
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
        };
        
        $imagenes = [
          'image_1' => [ 'file' => '', 'url' => $report->path_image('image_1')],
          'image_2' => [ 'file' => '', 'url' => $report->path_image('image_2')],
          'image_3' => [ 'file' => '', 'url' => $report->path_image('image_3')]
        ];

        return $this->respondHttp200([
            'data' => $imagenes
        ]);
    }

    public function base64($file, $column)
    {
      $image_64 = $file;

      $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

      $replace = substr($image_64, 0, strpos($image_64, ',')+1); 

      $image = str_replace($replace, '', $image_64); 

      $image = str_replace(' ', '+', $image); 

      $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . $column . '.' . $extension;

      $imagen = base64_decode($image);

      return ['name' => $imageName, 'image' => $imagen];

    } 

    public function checkImgStore($report, $image, $column)
    {
      $img = $this->base64($image, $column);
      $fileName = $img['name'];
      $file = $img['image'];

      $report->img_delete($column);
      $report->store_image($column, $fileName, $file);
    }

    public function checkImgRequest(&$images, $request, $key)
    {
      $index = "image_".$key;

      if ($request->has($index) && $request->$index['file'])
      {
        $images->push($request->$index['file']);
      }
    }*/

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

        $keywords = $this->getKeywordQueue($request->company_id);
        $confLocation = $this->getLocationFormConfModule($request->company_id);

      DB::beginTransaction();
        
      try
      {
        if ($request->id)
        {
          $report = Report::select(
            'sau_ph_reports.*',
            'sau_employees_regionals.name AS regionals',
            'sau_employees_headquarters.name AS sede',
            'sau_employees_processes.name AS proceso',
            'sau_employees_areas.name AS areas'
          )
          ->leftjoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_reports.employee_regional_id')
          ->leftjoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_reports.employee_headquarter_id')
          ->leftjoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_reports.employee_process_id')
          ->leftjoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_reports.employee_area_id')
          ->where('sau_ph_reports.id', $request->id);

          $report->company_scope = $request->company_id;
          $report = $report->first();
        }
        else
        {
          $report = new Report();
          $report->user_id = $this->user->id;
        }

        $report->company_id = $request->company_id;
        $report->observation = $request->observation;
        $report->rate = $request->rate;
        $report->condition_id = $request->condition_id;
        $report->employee_regional_id = $request->employee_regional_id;
        $report->employee_headquarter_id = $request->employee_headquarter_id ? $request->employee_headquarter_id : NULL;
        $report->employee_process_id = $request->employee_process_id ? $request->employee_process_id : NULL;
        $report->employee_area_id = $request->employee_area_id ? $request->employee_area_id : NULL;
        $report->other_condition = $request->other_condition ? $request->other_condition : NULL;
        $report->save();

        $image_1 = ImageApi::where('hash', $request->image_1['file'])->where('type', 1)->first();

        if ($image_1)
        {
          $report->img_delete('image_1');
          $report->image_1 = $image_1->file;
          $image_1->delete();
        }

        $image_2 = ImageApi::where('hash', $request->image_2['file'])->where('type', 1)->first();
        
        if ($image_2)
        {
          $report->img_delete('image_2');
          $report->image_2 = $image_2->file;
          $image_2->delete();
        }

        $image_3 = ImageApi::where('hash', $request->image_3['file'])->where('type', 1)->first();
        
        if ($image_3)
        {
          $report->img_delete('image_3');
          $report->image_3 = $image_3->file;
          $image_3->delete();
        }
        
        if (!$report->save())
            return $this->respondHttp500();

        if ($report->employee_regional_id)
        {
          $regional = EmployeeRegional::select('name')->where('id', $report->employee_regional_id);
          $regional->company_scope = $request->company_id;
          $regional = $regional->first();
          $report->regional_name = $regional->name;
        }
        if ($report->employee_headquarter_id)
          $report->headquarter_name = $report->headquarter->name;
        if ($report->employee_process_id)
          $report->process_name = $report->process->name;
        if ($report->employee_area_id)
          $report->area_name = $report->area->name;
        $report->condition_name = $report->condition->description;

        $details = $report->condition->conditionType->description. ': ' . $report->condition->description;

        if ($confLocation['regional'] == 'SI')
            $detail_procedence = 'Inspecciones no planesdas. Hallazgo: '. $report->condition->description . ' - ' . $keywords['regional']. ': ' .  $request->regional_name;
        if ($confLocation['headquarter'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $request->headquarter_name;
        if ($confLocation['process'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $request->process_name;
        if ($confLocation['area'] == 'SI')
            $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $request->area_name;

        ActionPlan::
                user($this->user)
            ->module('dangerousConditions')
            ->url(url('/administrative/actionplans'))
            ->model($report)
            ->regional($report->regionals ? $report->regionals : null)
            ->headquarter($report->sede ? $report->sede : null)
            ->area($report->areas ? $report->areas : null)
            ->process($report->proceso ? $report->proceso : null)
            ->details($details)
            ->detailProcedence($detail_procedence)
            ->activities($request->actionPlan)
            ->company($request->company_id)
            ->save()
            ->sendMail();

        /*ActionPlan::
              user($this->user)
          ->module('dangerousConditions')
          ->url(url('/administrative/actionplans'))
          ->model($report)
          ->activities($request->actionPlan)
          ->company($request->company_id)
          ->save()
          ->sendMail();*/
            
        DB::commit();

        $report->actionPlan = ActionPlan::getActivities();

        $report->image_1 = [ 'file' => '', 'url' => $report->path_image('image_1')];
        $report->image_2 = [ 'file' => '', 'url' => $report->path_image('image_2')];
        $report->image_3 = [ 'file' => '', 'url' => $report->path_image('image_3')];

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
      try
      {
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

        if (!$company->ph_state_incentives)
          return $this->respondWithError('No tiene incentivos activos');
        
        if (!$company->ph_file_incentives)
          return $this->respondWithError('No ha cargado incentivos');

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
