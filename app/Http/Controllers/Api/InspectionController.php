<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\Inspection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSection;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\LegalAspects\Contracts\Qualifications;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\Api\InspectionsRequest;
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
        /*if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }*/

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
        
        $inspections = Inspection::join('sau_ph_inspection_regional', 'sau_ph_inspection_regional.inspection_id', 'sau_ph_inspections.id')          
          ->where('employee_regional_id',$request->employee_regional_id)
          ->where('state', 'SI');

        if ($level >= 2)
        {
          $inspections->join('sau_ph_inspection_headquarter', 'sau_ph_inspection_headquarter.inspection_id', 'sau_ph_inspections.id')
          ->where('employee_headquarter_id',$request->employee_headquarter_id);
          
          if ($level >= 3)
          {
            $inspections->join('sau_ph_inspection_process', 'sau_ph_inspection_process.inspection_id', 'sau_ph_inspections.id')
            ->where('employee_process_id',$request->employee_process_id);

            if ($level >=4)
            {
              $inspections->join('sau_ph_inspection_area', 'sau_ph_inspection_area.inspection_id', 'sau_ph_inspections.id')            
              ->where('employee_area_id',$request->employee_area_id);
            }
          }
        }

        $inspections->company_scope = $request->company_id;

        $data = [];

        foreach ($inspections->get() as $key => $value)
        {
            $created_at = $value->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->toDateString() : '';

            $data[] = [
                'id' => $value->id,
                'name' => $value->name,
                'created_at' => $created_at
            ];
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
    public function create(Request $request)
    {
        if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }

        if (!$request->has('id'))
        {
            return response(json_encode([
                'response' => 'error',
                'data' => 'ID de la inspección requerido'
            ]), 422);
        }

        $inspection = Inspection::find($request->get('id'));
        $data = [];

        if ($inspection)
        {
            $data['id'] = $inspection->id;
            $data['name'] = $inspection->name;
            $data['created_at'] = $inspection->created_at != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inspection->created_at)->toDateString() : '';
            $data['sections'] = [];

            foreach ($inspection->sections as $keySection => $section)
            {
                $tmp_items = [];

                foreach($section->items as $keyItem => $item)
                {
                    $tmp_items[] = [
                        'id' => $item->id,
                        'description' => $item->description
                    ];
                }

                $data['sections'][] = [
                    'id' => $section->id,
                    'name' => $section->name,
                    'items' => $tmp_items
                ];
            }
        }
        else
        {
            return response(json_encode([
                'response' => 'error',
                'data' => 'Inspección no encontrada'
            ]), 404);
        }

        return response(json_encode([
            'response' => 'ok',
            'data' => $data
        ], JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * Stores the images of a given report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }

        $validator = Validator::make($request->all(), [
            'inspection_id' => 'required',
            'items' => 'required',
            'area_id' => 'required',
            'location_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(json_encode([
                'response' => 'error',
                'errors' => $validator->messages()
            ]), 422);
        }

        $inspection = Inspection::find($request->get('inspection_id'));

        if (!$inspection)
        {
            return response(json_encode([
                'response' => 'error',
                'data' => 'Inspección no encontrada'
            ]), 404);
        }

        DB::beginTransaction();

        $qualifier_id = $this->user->id;
        $area_id = $request->area_id;
        $location_id = $request->location_id;
        $qualification_date = date('Y-m-d H:i:s');

        try
        {
            $activitiesNew = [];

            foreach ($request->get('items') as $key => $value)
            {
                $item = new InspectionItemsQualificationAreaLocation();
                $item->item_id = $value["item_id"];
                $item->qualification_id = $value["qualification_id"];
                $item->find = isset($value["find"]) ? $value["find"] : '';
                $item->qualification_date = $qualification_date;
                $item->qualifier_id = $qualifier_id;
                $item->area_id = $area_id;
                $item->location_id = $location_id;
                $item->save();

                if ($item)
                {
                    if (isset($value["actions"]) && is_array($value["actions"]) && COUNT($value["actions"]) > 0)
                    {
                        foreach ($value["actions"] as $keyAction => $valueAction)
                        {
                            $action = new InspectionActionPlan();                            
                            $action->description = isset($valueAction["description"]) ? $valueAction["description"] : null;
                            $action->watch_date = isset($valueAction["watch_date"]) ? \Carbon\Carbon::parse($valueAction["watch_date"])->toDateTimeString() : null;
                            $action->exec_date = isset($valueAction["exec_date"]) ? \Carbon\Carbon::parse($valueAction["exec_date"])->toDateTimeString() : null;
                            $action->responsible_id = isset($valueAction["responsible_id"]) ? $valueAction["responsible_id"] : null;
                            $action->state = isset($valueAction["state"]) ? $valueAction["state"] : null;
                            $action->qualification_item_id = $item->id;
                            $action->save();

                            $data = $action->toArray();
                            $data['section'] = $item->item->section->name;
                            $data['item'] = $item->item->description;

                            array_push($activitiesNew, $data);
                        }
                    }
                }
            }

            //Envio de corre
            if(!$this->user->hasRole('company_supervisor')){
                ActionPlanInspection::sendMail($inspection->id, $item->id, collect($activitiesNew), $this->user->id, $this->user->company_id, url("/inspections/itemDetail/{$item->id}"));
            }

            DB::commit();
            

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);
            return response(json_encode([
                'response' => 'error',
                'data' => 'No se realizo la calificación',
            ], JSON_UNESCAPED_UNICODE), 500);
        }

        return json_encode([
            'response' => 'ok',
            'data' => base64_encode($qualification_date),
        ], JSON_UNESCAPED_UNICODE);
    }

        /**
     * Stores the images of a given report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function imageItem(Request $request)
    {
        if (!$this->user->hasPermission('create_reports')) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }
        $validator = Validator::make($request->all(), [
          'image' => 'required',
          'item_id' => 'required',
          'area_id' => 'required',
          'location_id' => 'required',
          'key' => 'required'
      ]);

        $image = $request->input('image');
        $item = InspectionItemsQualificationAreaLocation::where('item_id',$request->item_id)
                ->where('area_id',$request->area_id)->where('location_id',$request->location_id)->where('qualification_date', base64_decode($request->key))->first();

        if($item["photo_".$image] != null){
            return json_encode([
              'response' => 'ok',
              'data' => 'Este item ya tiene la imagen '.$image,
          ], JSON_UNESCAPED_UNICODE);
        };

        if (!$request->hasFile('file')) {
            return response(json_encode([
                    'response' => 'error',
                    'data' => 'No se envio imagen',
                ], JSON_UNESCAPED_UNICODE), 500);
        }

        if (!$request->file('file')->isValid()) {
            return response(json_encode([
                    'response' => 'error',
                    'data' => 'Imagen inválida',
                ], JSON_UNESCAPED_UNICODE), 500);
        }

       
        $fileName = md5($request->file('file')->getClientOriginalName() . \Carbon\Carbon::now()) . $item->id . 'image_' . $image . '.' . $request->file('file')->getClientOriginalExtension();
        $item->image($image, $fileName);

        if (!$item->save()) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'Hubo un problema guardando el reporte',
            ], JSON_UNESCAPED_UNICODE), 500);
        }
        Storage::disk('s3')->put('inspections_images/'.$fileName,
                        file_get_contents($request->file('file')->getRealPath()),'public');
        
        return json_encode([
            'response' => 'ok',
            'data' => 'Reporte guardado correctamente',
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the list of rating types
     *
     * @return \Illuminate\Http\Response
     */
    public function qualificationsList()
    {
        if (!$this->user->hasRole(['admin', 'company_admin', 'company_supervisor'])) {
            return response(json_encode([
                'response' => 'error',
                'data' => 'No autorizado'
            ]), 401);
        }
        
        $qualifications = Qualification::where('state', 1)->get();

        $data = [];

        foreach ($qualifications as $key => $value)
        {
            $data[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'description' => $value->description
                ];
        }

        return response(json_encode([
            'response' => 'ok',
            'data' => $data
        ], JSON_UNESCAPED_UNICODE), 200);
    }


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
                    'qualification_date' => $inspectionReady->qualification_date != '' ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inspectionReady->qualification_date)->toDateString() : '',
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
