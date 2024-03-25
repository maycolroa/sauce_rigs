<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\IndustrialSecure\RoadSafety\Vehicle;
use App\Models\IndustrialSecure\RoadSafety\Inspections\Inspection;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionFirm;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSection;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionSectionItem;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionQualified;
use App\Models\IndustrialSecure\RoadSafety\Inspections\InspectionItemsQualificationLocation;
use App\Models\IndustrialSecure\RoadSafety\Inspections\ImageApi;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\Api\InspectionsRequest;
use App\Http\Requests\Api\InspectionsCreateRequest;
use App\Http\Requests\Api\InspectionQualificationsRequest;
use App\Http\Requests\Api\ImageInspectionRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\General\PermissionService;
use Validator;
use Carbon\Carbon;
use DB;

class InspectionRoadSafetyController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
        parent::__construct();
    }

    public function saveImageApi(Request $request)
    {
      DB::beginTransaction();
          
        try
        {
          $img = $this->base64($request->image['image']);
          $fileName = $img['name'];
          $file = $img['image'];

          $image = new ImageApi;
          $image->file = $fileName;
          $image->type = $request->image['type'];
          $image->hash = $request->image['hash'];
          $image->save();

          (new InspectionItemsQualificationLocation)->store_image_api($fileName, $file);

          DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
          'data' => $request->image['id']
        ]);
    }

    public function getModuleRoadSafety(Request $request)
    {
      $module = PermissionService::getModuleByName('roadSafety');

      return $this->respondHttp200([
        'data' => [
          'module_road_safety' => PermissionService::existsLicenseByModule($request->company_id, $module->id)
        ]
      ]);
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
            }
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
            ]);

            $regionals = DB::table('sau_rs_inspection_regional')->where('inspection_id', $value->id)->pluck('employee_regional_id')->unique();
            $inspection->put('regionals', $regionals);

            if ($level >= 2)
            {
                $headquarters = DB::table('sau_rs_inspection_headquarter')->where('inspection_id', $value->id)->pluck('employee_headquarter_id')->unique();
                $inspection->put('headquarters', $headquarters);
            }

            if ($level >= 3)
            {
                $processes = DB::table('sau_rs_inspection_process')->where('inspection_id', $value->id)->pluck('employee_process_id')->unique();
                $inspection->put('processes', $processes);
            }

            if ($level >= 4)
            {
                $areas = DB::table('sau_rs_inspection_area')->where('inspection_id', $value->id)->pluck('employee_area_id')->unique();
                $inspection->put('areas', $areas);
            }

            $data->push($inspection);
        }

        return $this->respondHttp200([
            'data' => $data
        ]);
    }

    public function lisVehiclesAvailable(InspectionsRequest $request)
    {
        $vehicles = Vehicle::query();
        $vehicles->company_scope = $request->company_id;
        $data = collect([]);

        foreach ($vehicles->get() as $key => $value)
        {
            $created_at = $value->created_at != '' ? Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->toDateString() : '';
            $vehicle = collect([
                'id' => $value->id,
                'name' => $value->name,
                'created_at' => $created_at,
                'regionals' => $value->employee_regional_id,
                'headquarters' => $value->employee_headquarter_id,
                'processes' => $value->employee_process_id,
                'areas' => $value->employee_area_id,
                'name' => $value->plate
            ]);

            $data->push($vehicle);
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
        \Log::info($request);
        $keywords = $this->getKeywordQueue($request->company_id);
        $confLocation = $this->getLocationFormConfModule($request->company_id);;

        $response = $request->all();

        $inspection = Inspection::selectRaw("
          sau_rs_inspections.*
        ")
        ->where('sau_rs_inspections.id', $request->inspection_id)
        ->groupBy('sau_rs_inspections.id');

        $inspection->company_scope = $request->company_id;
        $inspection = $inspection->first();

        $vehicle = Vehicle::selectRaw("
          sau_rs_vehicles.*
        ")
        ->where('sau_rs_vehicles.id', $request->inspection_id)
        ->groupBy('sau_rs_vehicles.id');

        $vehicle->company_scope = $request->company_id;
        $vehicle = $vehicle->first();

        if (!$inspection)
        {
           return $this->respondWithError('Inspección no encontrada');
        }

        try
        {         
            DB::beginTransaction();


            $date = Carbon::now();

            $qualifier_id = $this->user->id;
            $qualification_date = $date->format('Y-m-d H:i:s'); 

            if (isset($response['qualified_id']) && $response['qualified_id'])
                $qualified = InspectionQualified::find($response['qualified_id'])->withoutGlobalScopes();
            else { 
                $qualified = new InspectionQualified();
            }

            $qualified->company_id = $request->company_id;
            $qualified->inspection_id = $inspection->id;
            $qualified->vehicle_id = $request->vehicle_id;
            $qualified->qualifier_id = $qualifier_id;
            $qualified->qualification_date = $qualification_date;
            $qualified->save();


            $response['qualified_id'] = $qualified->id;

            $employee_regional_id = $request->employee_regional_id ? $request->employee_regional_id : null;
            $employee_headquarter_id = $request->employee_headquarter_id ? $request->employee_headquarter_id : null;
            $employee_process_id = $request->employee_process_id ? $request->employee_process_id : null;
            $employee_area_id = $request->employee_area_id ? $request->employee_area_id : null;

            foreach ($request->themes as $keyT => $theme)
            {
                foreach ($theme['items'] as $key => $value)
                {
                    $fileName1 = null;
                    $fileName2 = null;

                    if (isset($value['id']) && $value['id'])
                        $item = InspectionItemsQualificationLocation::find($value['id'])->withoutGlobalScopes();
                    else { 
                        $item = new InspectionItemsQualificationLocation();
                    }

                    if ($inspection->type_id == 3 && $value['type_id'] == 2)
                        $value['qualify'] = implode(',', $value['qualify']);
                    elseif ($inspection->type_id == 3 && $value['type_id'] != 2) 
                        $item->qualify = $value["qualify"];
                    else 
                        $item->qualify = NULL;

                    $item->inspection_qualification_id = $qualified->id;
                    $item->item_id = $value["item_id"];
                    $item->theme_id = $theme['id'];
                    $item->qualification_id = $inspection->type_id == 3 ? 3 : $value["qualification_id"];                    
                    $item->find = isset($value["find"]) ? $value["find"] : '';
                    $item->level_risk = isset($value["level_risk"]) ? $value["level_risk"] : '';
                    $item->employee_regional_id = $employee_regional_id;
                    $item->employee_process_id = $employee_process_id;
                    $item->employee_area_id = $employee_area_id;
                    $item->employee_headquarter_id = $employee_headquarter_id;
                    $item->save();
                
                    $response['themes'][$keyT]['items'][$key]['id'] = $item->id;

                    if (isset($value['photos']))
                    {
                        $photo_1 = ImageApi::where('hash', $value['photos']['photo_1']['file'])->where('type', 1)->first();

                        if ($photo_1)
                        {
                          $item->img_delete('photo_1');
                          $item->photo_1 = $photo_1->file;
                          $photo_1->delete();
                        }

                        $photo_2 = ImageApi::where('hash', $value['photos']['photo_2']['file'])->where('type', 1)->first();
                        
                        if ($photo_2)
                        {
                          $item->img_delete('photo_2');
                          $item->photo_2 = $photo_2->file;
                          $photo_2->delete();
                        }

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

                        $details = 'Inspección:' . $inspection->name . ' - Tema: ' . $theme->name . ' - Item: ' . $itemName->description. ' - ';

                        if ($confLocation['regional'] == 'SI')
                            $detail_procedence = 'Inspecciones Planeadas Vehiculos. Placa de vehiculo: '. $vehicle->plate. '. ' . $details . ' - ' . $keywords['regional']. ': ' .  $regional_detail->name;
                        if ($confLocation['headquarter'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['headquarter']. ': ' .  $headquarter_detail->name;
                        if ($confLocation['process'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['process']. ': ' .  $process_detail->name;
                        if ($confLocation['area'] == 'SI')
                            $detail_procedence = $detail_procedence . ' - ' .$keywords['area']. ': ' .  $area_detail->name;

                        ActionPlan::
                            user($this->user)
                        ->module('roadSafety')
                        ->url(url('/administrative/actionplans'))
                        ->model($item)
                        ->regional($regional_detail)
                        ->headquarter($headquarter_detail)
                        ->area($area_detail)
                        ->process($process_detail)
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


            if ($request->has('firms') && $request->firms)
            {
                foreach ($request->firms['firmsAdd'] as $key => $firms) 
                {               
                    if (isset($firms['type']))
                    {   
                        if ($firms['type'] == 'ingresar')
                        {
                            if ($firms['image'])
                            {
                                $exist_firm = InspectionFirm::where('inspection_qualification_id', $qualified->id)->where('identification', $firms['identification'])->first();

                                if ($exist_firm)
                                {
                                    $img_firm = ImageApi::where('hash', $firms['image'])->where('type', 3)->first();

                                    if ($img_firm)
                                        $exist_firm->image = $img_firm->file;
                                    else
                                        $exist_firm->image = $firms['image'];

                                    $exist_firm->name = $firms['name'];
                                    $exist_firm->state = 'Ingresada';
                                    $exist_firm->identification = $firms['identification'];
                                    $exist_firm->inspection_qualification_id = $qualified->id;
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
                                    $exist_firm->state = 'Ingresada';
                                    $exist_firm->identification = $firms['identification'];
                                    $exist_firm->company_id = $request->company_id;
                                    $exist_firm->inspection_qualification_id = $qualified->id;
                                    $exist_firm->save();
                                }

                                $response['firms']['firmsAdd'][$key] = [
                                    "id" => $exist_firm->id, "name" => $exist_firm->name, "identification" => $exist_firm->identification, "image" => $exist_firm->image, "type" => $firms['type']
                                ];
                            }
                        }
                        else
                        {
                            $user_solicitud = User::find($firms['firm_solicitud']['value']);

                            $exist_firm = new InspectionFirm;
                            $exist_firm->name = $firms['name'];
                            $exist_firm->state = 'Pendiente';
                            $exist_firm->company_id = $request->company_id;
                            $exist_firm->user_id = $user_solicitud->id;
                            $exist_firm->identification = $user_solicitud->document;
                            $exist_firm->inspection_qualification_id = $qualified->id;
                            $exist_firm->save();

                            $response['firms']['firmsAdd'][$key] = [
                                "id" => $exist_firm->id, "name" => $exist_firm->name, "identification" => $exist_firm->identification, "image" => $exist_firm->image, "type" => $firms['type']
                            ];
                        }
                    }
                    else 
                    {
                        if ($firms['image'])
                        {
                            $exist_firm = InspectionFirm::where('inspection_qualification_id', $qualified->id)->where('identification', $firms['identification'])->first();

                            if ($exist_firm)
                            {
                                $img_firm = ImageApi::where('hash', $firms['image'])->where('type', 3)->first();
                                
                                if ($img_firm)
                                    $exist_firm->image = $img_firm->file;
                                else
                                    $exist_firm->image = $firms['image'];

                                $exist_firm->name = $firms['name'];
                                $exist_firm->state = 'Ingresada';
                                $exist_firm->identification = $firms['identification'];
                                $exist_firm->inspection_qualification_id = $qualified->id;
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
                                $exist_firm->state = 'Ingresada';
                                $exist_firm->identification = $firms['identification'];
                                $exist_firm->company_id = $request->company_id;
                                $exist_firm->inspection_qualification_id = $qualified->id;
                                $exist_firm->save();
                            }

                            $response['firms']['firmsAdd'][$key] = [
                                "id" => $exist_firm->id, "name" => $exist_firm->name, "identification" => $exist_firm->identification, "image" => $exist_firm->image, "type" => 'ingresar'
                            ];
                        }
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
}
