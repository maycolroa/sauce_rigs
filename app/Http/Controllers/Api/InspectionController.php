<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFields;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\QualificationRepeat;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\AdditionalFieldsValues;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Users\User;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\Api\InspectionsRequest;
use App\Http\Requests\Api\InspectionsCreateRequest;
use App\Http\Requests\Api\InspectionQualificationsRequest;
use App\Http\Requests\Api\ImageInspectionRequest;
use Validator;
use Carbon\Carbon;
use DB;

class InspectionController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lisInspectionsAvailable(InspectionsRequest $request)
    {
        $location = $this->getLocationFormConfModule($request->company_id);

        $level = "";

        if ($location['area'] == 'SI')
            $level = 4;
        else if ($location['process'] == 'SI')
            $level = 3;
        else if ($location['headquarter'] == 'SI')
            $level = 2;
        else
            $level = 1;

        $inspections = Inspection::with([
            'themes' => function ($query) {
                $query->with('items');
            },
            'additional_fields'
        ]);

        $inspections->company_scope = $request->company_id;
        $data = collect([]);

        foreach ($inspections->get() as $key => $value)
        {
            $created_at = $value->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->toDateString() : '';
            $inspection = collect([
                'id' => $value->id,
                'name' => $value->name,
                'created_at' => $created_at,
                'themes' => $value->themes,
                'additional_fields' => $value->additional_fields
            ]);

            $regionals = DB::table('sau_ph_inspection_regional')->where('inspection_id', $value->id)->pluck('employee_regional_id')->unique();
            $inspection->put('regionals', $regionals);

            if ($level >= 2)
            {
                $headquarters = DB::table('sau_ph_inspection_headquarter')->where('inspection_id', $value->id)->pluck('employee_headquarter_id')->unique();
                $inspection->put('headquarters', $headquarters);
            }

            if ($level >= 3)
            {
                $processes = DB::table('sau_ph_inspection_process')->where('inspection_id', $value->id)->pluck('employee_process_id')->unique();
                $inspection->put('processes', $processes);
            }

            if ($level >= 4)
            {
                $areas = DB::table('sau_ph_inspection_area')->where('inspection_id', $value->id)->pluck('employee_area_id')->unique();
                $inspection->put('areas', $areas);
            }

            $data->push($inspection);
        }

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(InspectionsCreateRequest $request)
    {
        /*if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }*/

        $inspection = Inspection::query();
        $inspection->company_scope = $request->company_id;
        $inspection = $inspection->find($request->inspection_id);

        $data = [];

        if ($inspection)
        {
            $data['id'] = $inspection->id;
            $data['name'] = $inspection->name;
            $data['created_at'] = $inspection->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $inspection->created_at)->toDateString() : '';
            $data['sections'] = [];

            foreach ($inspection->themes as $keyThemes => $theme)
            {
                $tmp_items = [];

                foreach($theme->items as $keyItem => $item)
                {
                    $tmp_items[] = [
                        'id' => $item->id,
                        'description' => $item->description
                    ];
                }

                $data['themes'][] = [
                    'id' => $theme->id,
                    'name' => $theme->name,
                    'items' => $tmp_items
                ];
            }
        }
        else
        {
            return $this->respondWithError('Inspección no encontrada');
        }

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    /**
     * Get the list of rating types
     *
     * @return \Illuminate\Http\Response
     */
    public function qualificationsList()
    {
        /*if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }*/
        
        $qualifications = Qualifications::all();

        $data = [];

        foreach ($qualifications as $key => $value)
        {
            $data[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'description' => $value->description
                ];
        }

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    /**
     * Stores the images of a given report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InspectionQualificationsRequest $request)
    {
        /*if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }*/

        $keywords = $this->getKeywordQueue($request->company_id);
        $confLocation = $this->getLocationFormConfModule($request->company_id);;

        $response = $request->all();

        $inspection = Inspection::selectRaw("
          sau_ph_inspections.*,
          GROUP_CONCAT(DISTINCT sau_employees_headquarters.name ORDER BY sau_employees_headquarters.name ASC) AS sede,
          GROUP_CONCAT(DISTINCT sau_employees_areas.name ORDER BY sau_employees_areas.name ASC) AS area,
          GROUP_CONCAT(DISTINCT sau_employees_regionals.name ORDER BY sau_employees_regionals.name ASC) AS regional,
          GROUP_CONCAT(DISTINCT sau_employees_processes.name ORDER BY sau_employees_processes.name ASC) AS proceso")
        ->leftJoin('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_ph_inspection_headquarter.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_ph_inspection_area.employee_area_id')
        ->leftJoin('sau_ph_inspection_regional', 'sau_ph_inspection_regional.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_ph_inspection_process', 'sau_ph_inspection_process.inspection_id', 'sau_ph_inspections.id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_ph_inspection_regional.employee_regional_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_ph_inspection_process.employee_process_id')
        ->where('sau_ph_inspections.id', $request->inspection_id)
        ->groupBy('sau_ph_inspections.id');

        $inspection->company_scope = $request->company_id;
        $inspection = $inspection->first();

        $regionals = $inspection->regional ? $inspection->regional : null;
        $headquarters =  $inspection->sede ? $inspection->sede : null;
        $processes = $inspection->proceso ? $inspection->proceso : null;
        $areas = $inspection->area ? $inspection->area : null;

        if (!$inspection)
        {
           return $this->respondWithError('Inspección no encontrada');
        }

        try
        {
            DB::beginTransaction();

            $qualifier_id = $this->user->id;
            $employee_regional_id = $request->employee_regional_id ? $request->employee_regional_id : null;
            $employee_headquarter_id = $request->employee_headquarter_id ? $request->employee_headquarter_id : null;
            $employee_process_id = $request->employee_process_id ? $request->employee_process_id : null;
            $employee_area_id = $request->employee_area_id ? $request->employee_area_id : null;
            $qualification_date = date('Y-m-d H:i:s');

            $qualification_date_verify = '';

            foreach ($request->themes as $keyT => $theme)
            {
                foreach ($theme['items'] as $key => $value)
                {
                    $fileName1 = null;
                    $fileName2 = null;

                    if (isset($value['id']) && $value['id'])
                        $item = InspectionItemsQualificationAreaLocation::find($value['id']);
                    else { 
                        $item = new InspectionItemsQualificationAreaLocation();
                        $item->qualification_date = $qualification_date;
                    }

                    $qualification_date_verify = $item->qualification_date;

                    $item->item_id = $value["item_id"];
                    $item->qualification_id = $value["qualification_id"];
                    $item->find = isset($value["find"]) ? $value["find"] : '';
                    $item->qualifier_id = $qualifier_id;
                    $item->employee_regional_id = $employee_regional_id;
                    $item->employee_process_id = $employee_process_id;
                    $item->employee_area_id = $employee_area_id;
                    $item->employee_headquarter_id = $employee_headquarter_id;
                    $item->save();
                

                    $response['themes'][$keyT]['items'][$key]['id'] = $item->id;

                    if (isset($value['photos']))
                    {
                        $photo_1 = ImageApi::where('hash', $value['photos']['photo_1']['file'])->where('type', 2)->first();

                        if ($photo_1)
                        {
                          $item->img_delete('photo_1');
                          $item->photo_1 = $photo_1->file;
                          $photo_1->delete();
                        }

                        $photo_2 = ImageApi::where('hash', $value['photos']['photo_2']['file'])->where('type', 2)->first();
                        
                        if ($photo_2)
                        {
                          $item->img_delete('photo_2');
                          $item->photo_2 = $photo_2->file;
                          $photo_2->delete();
                        }

                        /*$images = collect([]);
                        $this->checkImgRequest($images, $value['photos'], 1);
                        $this->checkImgRequest($images, $value['photos'], 2);

                        $i = 1;
                        $replace = false;

                        while ($images->count() > 0)
                        {
                        $column = "photo_".$i;

                        if (!$item->$column || $replace)
                        {
                            $this->checkImgStore($item, $images->pop(), $column);
                        }

                        if ($i == 2)
                        {
                            $i = 1;
                            $replace = true;
                        }
                        else
                            $i++;
                        };*/

                        $item->update();

                        $response['themes'][$keyT]['items'][$key]["photos"] = [
                            "photo_1" => ["file" => "", "url" => $item->path_image('photo_1')],
                            "photo_2" => ["file" => "", "url" => $item->path_image('photo_2')]
                        ];
                    }

                    if (isset($value["actionPlan"]))
                    {

                        $regional_detail = EmployeeRegional::where('id', $employee_regional_id);
                        $regional_detail->company_scope = $request->company_id;
                        $regional_detail = $regional_detail->first();
                        $headquarter_detail = EmployeeHeadquarter::find($employee_headquarter_id);
                        $process_detail = EmployeeProcess::find($employee_process_id);
                        $area_detail = EmployeeArea::find($employee_area_id);

                        $theme = $item->item->section;
                        $itemName = $item->item;
                        $details = 'Inspección: ' . $inspection->name . ' - Tema: ' . $theme->name . ' - Item: ' . $itemName->description;

                        if ($confLocation['regional'] == 'SI')
                            $detail_procedence = 'Inspecciones Planeadas. ' . $details . ' - ' . $keywords['regional']. ': ' .  $regional_detail->name;
                        if ($confLocation['headquarter'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $headquarter_detail->name;
                        if ($confLocation['process'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $process_detail->name;
                        if ($confLocation['area'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $area_detail->name;

                        ActionPlan::
                            user($this->user)
                        ->module('dangerousConditions')
                        ->url(url('/administrative/actionplans'))
                        ->model($item)
                        ->regional($regionals)
                        ->headquarter($headquarters)
                        ->area($areas)
                        ->process($processes)
                        ->activities($value["actionPlan"])                
                        ->company($request->company_id)
                        ->details($details)
                        ->detailProcedence($detail_procedence)
                        ->dateSimpleFormat(true)
                        ->save()
                        ->sendMail();

                        $response['themes'][$keyT]['items'][$key]['actionPlan'] = ActionPlan::getActivities();

                        ActionPlan::restart();
                    }
                }
            }

            if ($request->has('additional_fields') && $request->additional_fields)
            {
                foreach ($request->additional_fields as $add) 
                {
                    $field = AdditionalFields::find($add['id']);

                    if($field)
                    {
                        $exist_add = AdditionalFieldsValues::where('qualification_date', $qualification_date_verify)->where('field_id', $field->id)->first();

                        if ($exist_add)
                        {
                            $exist_add->field_id = $field->id;
                            $exist_add->value = $add['value'];
                            $exist_add->qualification_date = $qualification_date_verify;
                            $exist_add->update();
                        }
                        else
                        {
                            $field_value = new AdditionalFieldsValues;
                            $field_value->field_id = $field->id;
                            $field_value->value = $add['value'];
                            $field_value->qualification_date = $qualification_date_verify;
                            $field_value->save();
                        }
                    }
                }
            }

            if ($request->has('repeat_date') && $request->repeat_date)
            {
                $add_fields_repeat = [];

                if ($request->has('additional_fields') && $request->additional_fields)
                {
                    foreach ($request->additional_fields as $add) 
                    {
                        array_push( $add_fields_repeat, $add['name']);
                    }
                }

                $mails_notify = [];

                if ($request->has('repeat_emails') && $request->repeat_emails)
                {
                    foreach ($request->repeat_emails as $email)
                    {
                        $mails = User::find($email['value']);
                        array_push($mails_notify, $mails->email);
                    }
                }

                $regionalRepeat = EmployeeRegional::where('id', $employee_regional_id);
                $regionalRepeat->company_scope = $request->company_id;
                $regionalRepeat = $regionalRepeat->first();
                $headquarterRepeat = EmployeeHeadquarter::find($employee_headquarter_id);
                $processRepeat = EmployeeProcess::find($employee_process_id);
                $areaRepeat = EmployeeArea::find($employee_area_id);

                $exist_repeat = QualificationRepeat::where('qualification_date', $qualification_date_verify);
                $exist_repeat = $exist_repeat->first();

                if ($exist_repeat)
                {
                    $exist_repeat->inspection_id = $request->inspection_id;
                    $exist_repeat->user_id = $this->user->id;
                    $exist_repeat->regional = $regionalRepeat->name;
                    $exist_repeat->headquarter = $headquarterRepeat ? $headquarterRepeat->name : null;
                    $exist_repeat->process = $processRepeat ? $processRepeat->name : null;
                    $exist_repeat->area = $areaRepeat ? $areaRepeat->name : null;
                    $exist_repeat->fields_adds = $add_fields_repeat ? implode($add_fields_repeat, ',') : null;
                    $exist_repeat->send_emails = $mails_notify ? implode($mails_notify, ',') : null;
                    $exist_repeat->qualification_date = $qualification_date;
                    $exist_repeat->repeat_date = $request->repeat_date;
                    $exist_repeat->update(); 
                }
                else
                {
                    $repeat = new QualificationRepeat;
                    $repeat->inspection_id = $request->inspection_id;
                    $repeat->user_id = $this->user->id;
                    $repeat->regional = $regionalRepeat->name;
                    $repeat->headquarter = $headquarterRepeat ? $headquarterRepeat->name : null;
                    $repeat->process = $processRepeat ? $processRepeat->name : null;
                    $repeat->area = $areaRepeat ? $areaRepeat->name : null;
                    $repeat->fields_adds = $add_fields_repeat ? implode($add_fields_repeat, ',') : null;
                    $repeat->send_emails = $mails_notify ? implode($mails_notify, ',') : null;
                    $repeat->qualification_date = $qualification_date_verify;
                    $repeat->repeat_date = $request->repeat_date;
                    $repeat->save();
                }                
            }


            if ($request->has('firms') && $request->firms)
            {
                foreach ($request->firms['firmsAdd'] as $key => $firms) 
                {                    
                    if ($firms['image'])
                    {
                        $exist_firm = InspectionFirm::where('qualification_date', $qualification_date_verify)->where('identification', $firms['identification'])->first();

                        if ($exist_firm)
                        {
                            $img_firm = ImageApi::where('hash', $firms['image'])->where('type', 3)->first();

                            if ($img_firm)
                                $exist_firm->image = $img_firm->file;
                            else
                                $exist_firm->image = $firms['image'];

                            $exist_firm->name = $firms['name'];
                            $exist_firm->identification = $firms['identification'];
                            $exist_firm->qualification_date = $qualification_date_verify;
                            $exist_firm->update();
                        }
                        else
                        {
                            $exist_firm = new InspectionFirm;

                            $img_firm = ImageApi::where('hash', $firms['image'])->where('type', 3)->first();

                            if ($img_firm)
                                $exist_firm->image = $img_firm->file;
                            else
                                $exist_firm->image = $firms['image'];

                            $exist_firm->name = $firms['name'];
                            $exist_firm->identification = $firms['identification'];
                            $exist_firm->qualification_date = $qualification_date_verify;
                            $exist_firm->save();
                        }

                        $response['firms']['firmsAdd'][$key] = [
                            "id" => $exist_firm->id, "name" => $exist_firm->name, "identification" => $exist_firm->identification, "image" => $exist_firm->image
                        ];
                    }
                }

                foreach ($request->firms['firmsRemoved'] as $key3 => $firmR)
                {
                    $removeFirm = InspectionFirm::query();
                    $removeFirm = $removeFirm->find($firmR['id']);

                    if ($removeFirm)
                    {
                        $removeFirm->img_delete('image');
                        $removeFirm->delete();
                    }
                }

                $response['firms']['firmsRemoved'] = [];
            }
            
            DB::commit();

        } catch (\Exception $e) {
          \Log::info($e->getMessage());
          DB::rollback();
          return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'data' => $response
        ]);
    }

    /*public function checkImgStore($item, $image, $column)
    {
      $img = $this->base64($image, $column);
      $fileName = $img['name'];
      $file = $img['image'];

      $item->img_delete($column);
      $item->store_image($column, $fileName, $file);
    }

    public function checkImgRequest(&$images, $value, $key)
    {
      $index = "photo_".$key;

      if (isset($value[$index]) && $value[$index]['file'])
      {
        $images->push($value[$index]['file']);
      }
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

    }*/

    /**
     * Get the list of inspeccions quelified for user
     *
     * @return \Illuminate\Http\Response
     */
    public function quelifiedListUser()
    {
      try{
        if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }
        
        //$inspections = Inspection::where('qualifier_id', $this->user->id)->orderBy('execution_date','desc')->paginate(15);
        $inspectionsReady = InspectionItemsQualificationAreaLocation::distinct()->select(
          'ph_areas.name as area',
          'ph_locations.name as location',
          'ph_inspection_items_qualification_area_location.location_id as location_id',
          'ph_inspection_items_qualification_area_location.area_id as area_id',
           'qualification_date',
           'inspection_id')
           ->join('ph_inspection_section_items', 'ph_inspection_section_items.id', '=', 'ph_inspection_items_qualification_area_location.item_id')
            ->join('ph_inspection_sections','ph_inspection_sections.id','=','ph_inspection_section_items.inspection_section_id')
            ->join('ph_locations', 'ph_inspection_items_qualification_area_location.location_id', '=', 'ph_locations.id')
            ->join('ph_areas', 'ph_inspection_items_qualification_area_location.area_id', '=', 'ph_areas.id')
           ->where('qualifier_id', $this->user->id)
           ->orderBy('qualification_date','desc')->paginate(15);

           $data = [];

        foreach ($inspectionsReady as $key => $inspectionReady)
        {
          $inspection = Inspection::find($inspectionReady->inspection_id);
          $current_inspection = [
                    'id' => $inspection->id,
                    'name' => $inspection->name,
                    'area' => $inspectionReady->area,
                    'location' => $inspectionReady->location,
                    'qualification_date' => $inspectionReady->qualification_date != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $inspectionReady->qualification_date)->toDateString() : '',
                    'sections' => []
            ];
            

            $items = InspectionItemsQualificationAreaLocation::where('location_id', $inspectionReady->location_id)->where('area_id', $inspectionReady->area_id)
                                                      ->where('qualification_date', $inspectionReady->qualification_date)->get();
            
            $tmp_items = [];
            //dd($items[0]->actions()->toSql());
            foreach($items as $keyItem => $item)
            {
              $action_plans = [];

                  foreach ($item->actions as $key => $action) {
                    array_push($action_plans, [
                      'id' => $action->id,
                      'description' => $action->description,
                        'responsible' => $action->responsible()->pluck('name')->first(),
                        'exec_date' => $action->exec_date != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $action->exec_date)->toDateString() : '',
                        'watch_date' => $action->watch_date != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $action->watch_date)->toDateString() : '',
                        'state' => $action->state
                    ]);
                  }

                  
                  $temp_section = [];

                  if(in_array($item->item->section->id, array_column($current_inspection['sections'], 'id'))){
                    $index = array_search($item->item->section->id, array_column($current_inspection['sections'], 'id'));
                    $temp_section = $current_inspection['sections'][$index];
                    array_splice($current_inspection['sections'],$index,1);

                  }else{
                    $temp_section = [
                      'id' =>$item->item->section->id,
                      'name' => $item->item->section->name,
                      'items' => []
                    ];
                  }
                  
                  array_push($temp_section["items"],[
                    'id' => $item->id,
                    'description' => $item->item->description,
                    'qualification' => [
                      'name' => $item->qualification->name,
                      'description' => $item->qualification->description 
                    ],
                    'find' => $item->find,
                    'photo_1' => $item->photo_1,
                    'photo_2' => $item->photo_2,
                    'action_plans' => $action_plans,
                  ]);
                  array_push($current_inspection['sections'], $temp_section);
            }
            

            array_push($data, $current_inspection);
        }

        return response(json_encode([
            'response' => 'ok',
            'total' =>$inspectionsReady->total(),
            'per_page' =>$inspectionsReady->perPage(),
            'current_page' =>$inspectionsReady->currentPage(),
            'last_page' =>$inspectionsReady->lastPage(),
            'next_page_url' =>$inspectionsReady->nextPageUrl(),
            'prev_page_url' =>$inspectionsReady->previousPageUrl(),
            'from' =>$inspectionsReady->firstItem(),
            'to' =>$inspectionsReady->lastItem(),
            'data' => $data
        ], JSON_UNESCAPED_UNICODE), 200);
      }
      catch(\Exception $e){
        \Log::error($e);
        return response(json_encode([
          'response' => 'error',
          'data' => 'Error inesperado, intentelo mas tarde.',
        ], JSON_UNESCAPED_UNICODE), 500);
      }
    }
}
