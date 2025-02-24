<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Vehicle;
use App\Models\IndustrialSecure\RoadSafety\TagsCapacityLoading;
use App\Models\IndustrialSecure\RoadSafety\TagsColor;
use App\Models\IndustrialSecure\RoadSafety\TagsLine;
use App\Models\IndustrialSecure\RoadSafety\TagsMark;
use App\Models\IndustrialSecure\RoadSafety\TagsModel;
use App\Models\IndustrialSecure\RoadSafety\TagsNamePropietary;
use App\Models\IndustrialSecure\RoadSafety\TagsPlate;
use App\Models\IndustrialSecure\RoadSafety\TagsTypeVehicle;
use App\Models\IndustrialSecure\RoadSafety\HistoryChanges;
use App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Traits\Filtertrait;
use DB;

class VehiclesController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:roadsafety_vehicles_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:roadsafety_vehicles_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:roadsafety_vehicles_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:roadsafety_vehicles_d, {$this->team}", ['only' => 'destroy']);
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
        $vehicles = Vehicle::select(
            'sau_rs_vehicles.*',
            'sau_employees_regionals.name AS regional',
            'sau_employees_headquarters.name AS headquarter',
            'sau_employees_processes.name AS process',
            'sau_employees_areas.name AS area'
        )
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rs_vehicles.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rs_vehicles.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rs_vehicles.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rs_vehicles.employee_process_id')
        ->orderBy('id', 'DESC');

        $url = "/industrialsecure/roadsafety/vehicles";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["typeVehicle"]))
                $vehicles->inTypeVehicle($this->getValuesForMultiselect($filters["typeVehicle"]), $filters['filtersType']['typeVehicle']);

            if (isset($filters["regionals"]))
                $vehicles->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $vehicles->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $vehicles->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $vehicles->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);

            $dates_request_soat = explode('/', $filters["dateRangeSoat"]);
            $dates_request_tm = explode('/', $filters["dateRangeTm"]);
            $dates_request_rc = explode('/', $filters["dateRangeRc"]);

            $dates_soat = [];
            $dates_tm = [];
            $dates_rc = [];

            if (COUNT($dates_request_soat) == 2)
            {
                array_push($dates_soat, $this->formatDateToSave($dates_request_soat[0]));
                array_push($dates_soat, $this->formatDateToSave($dates_request_soat[1]));
            }
                
            $vehicles->betweenDateSoat($dates_soat);

            if (COUNT($dates_request_tm) == 2)
            {
                array_push($dates_tm, $this->formatDateToSave($dates_request_tm[0]));
                array_push($dates_tm, $this->formatDateToSave($dates_request_tm[1]));
            }
                
            $vehicles->betweenDateTm($dates_tm);

            if (COUNT($dates_request_rc) == 2)
            {
                array_push($dates_rc, $this->formatDateToSave($dates_request_rc[0]));
                array_push($dates_rc, $this->formatDateToSave($dates_request_rc[1]));
            }
                
            $vehicles->betweenDateRc($dates_rc);
        }

        return Vuetable::of($vehicles)
                    ->make();
    }

    public function dataSoat(Request $request)
    {
        $soat = HistoryChanges::select(
            'sau_rs_history_records_vehicles.*',
            'sau_users.name AS user'
        )
        ->join('sau_users', 'sau_users.id', 'sau_rs_history_records_vehicles.user_id')
        ->where('sau_rs_history_records_vehicles.type', 'soat')
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $soat->where('sau_rs_history_records_vehicles.vehicle_id', '=', $request->get('modelId'));

        return Vuetable::of($soat)
                    ->make();
    }

    public function dataMechanical(Request $request)
    {
        $mechanical = HistoryChanges::select(
            'sau_rs_history_records_vehicles.*',
            'sau_users.name AS user'
        )
        ->join('sau_users', 'sau_users.id', 'sau_rs_history_records_vehicles.user_id')
        ->where('sau_rs_history_records_vehicles.type', 'mechanical')
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $mechanical->where('sau_rs_history_records_vehicles.vehicle_id', '=', $request->get('modelId'));

        return Vuetable::of($mechanical)
                    ->make();
    }

    public function dataResponsability(Request $request)
    {
        $responsability = HistoryChanges::select(
            'sau_rs_history_records_vehicles.*',
            'sau_users.name AS user'
        )
        ->join('sau_users', 'sau_users.id', 'sau_rs_history_records_vehicles.user_id')
        ->where('sau_rs_history_records_vehicles.type', 'responsability')
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $responsability->where('sau_rs_history_records_vehicles.vehicle_id', '=', $request->get('modelId'));

        return Vuetable::of($responsability)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\VehicleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        DB::beginTransaction();

        try
        {
            /*$plate = $this->tagsPrepare($request->get('plate'));
            $this->tagsSave($plate, TagsPlate::class);*/

            $name_propietary = $this->tagsPrepare($request->get('name_propietary'));
            $this->tagsSave($name_propietary, TagsNamePropietary::class);

            $type_vehicle = $this->tagsPrepare($request->get('type_vehicle'));
            $this->tagsSave($type_vehicle, TagsTypeVehicle::class);

            $mark = $this->tagsPrepare($request->get('mark'));
            $this->tagsSave($mark, TagsMark::class);

            $line = $this->tagsPrepare($request->get('line'));
            $this->tagsSave($line, TagsLine::class);

            $model = $this->tagsPrepare($request->get('model'));
            $this->tagsSave($model, TagsModel::class);

            $color = $this->tagsPrepare($request->get('color'));
            $this->tagsSave($color, TagsColor::class);

            $loading_capacity = $this->tagsPrepare($request->get('loading_capacity'));
            $this->tagsSave($loading_capacity, TagsCapacityLoading::class);

            $vehicle = new Vehicle;
            $vehicle->company_id = $this->company;

            ///General
            $vehicle->plate = $request->plate;//$plate->implode(',');
            $vehicle->name_propietary = $name_propietary->implode(',');
            $vehicle->registration_number = $request->registration_number;
            $vehicle->registration_number_date = $request->registration_number_date ? (Carbon::createFromFormat('D M d Y', $request->registration_number_date))->format('Y-m-d') : null;
            $vehicle->type_vehicle = $type_vehicle->implode(',');
            $vehicle->code_vehicle = $request->code_vehicle;

            ///Informacion matricula
            $vehicle->mark = $mark->implode(',');
            $vehicle->line = $line->implode(',');
            $vehicle->model = $model->implode(',');
            $vehicle->cylinder_capacity = $request->cylinder_capacity;
            $vehicle->color = $color->implode(',');
            $vehicle->chassis_number = $request->chassis_number;
            $vehicle->engine_number = $request->engine_number;
            $vehicle->passenger_capacity = $request->passenger_capacity;
            $vehicle->loading_capacity = $loading_capacity->implode(',');
            $vehicle->state = $request->state;

            ////SOAT
            $vehicle->soat_number = $request->soat_number;
            $vehicle->insurance = $request->insurance;
            //$vehicle->required_due_date_soat = $request->required_due_date_soat;
            $vehicle->expedition_date_soat = $request->expedition_date_soat ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_soat))->format('Y-m-d') : null;
            $vehicle->due_date_soat = $request->due_date_soat ? (Carbon::createFromFormat('D M d Y', $request->due_date_soat))->format('Y-m-d') : null;

            $path = "industrialSecure/roadSafety/files/".$this->company."/";

            if ($request->file_soat)
            {
                $file_tmp = $request->file_soat;
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp->getClientOriginalExtension();
                $file_tmp->storeAs($path, $nameFile, 's3');
                $vehicle->file_soat = $nameFile;
            }

            //Tecno mecanica
            $vehicle->mechanical_tech_number = $request->mechanical_tech_number;
            $vehicle->issuing_entity = $request->issuing_entity;
            $vehicle->expedition_date_mechanical_tech = $request->expedition_date_mechanical_tech ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_mechanical_tech))->format('Y-m-d') : null;
            //$vehicle->required_due_date_mechanical_tech = $request->required_due_date_mechanical_tech;
            $vehicle->due_date_mechanical_tech = $request->due_date_mechanical_tech ? (Carbon::createFromFormat('D M d Y', $request->due_date_mechanical_tech))->format('Y-m-d') : null;

            if ($request->file_mechanical_tech)
            {
                $file_tmp_2 = $request->file_mechanical_tech;
                $nameFile_2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp_2->getClientOriginalExtension();
                $file_tmp_2->storeAs($path, $nameFile_2, 's3');
                $vehicle->file_mechanical_tech = $nameFile_2;
            }

            //Responsabilidad civil
            $vehicle->policy_responsability = $request->policy_responsability;
            $vehicle->policy_number = $request->policy_number;
            $vehicle->policy_entity = $request->policy_entity;
            $vehicle->expedition_date_policy = $request->expedition_date_policy ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_policy))->format('Y-m-d') : null;
            //$vehicle->required_due_date_policy = $request->required_due_date_policy;
            $vehicle->due_date_policy = $request->due_date_policy ? (Carbon::createFromFormat('D M d Y', $request->due_date_policy))->format('Y-m-d') : null;

            if ($request->file_policy)
            {
                $file_tmp_3 = $request->file_policy;
                $nameFile_3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_tmp_3->getClientOriginalExtension();
                $file_tmp_3->storeAs($path, $nameFile_3, 's3');
                $vehicle->file_policy = $nameFile_3;
            }

            if (!$vehicle->save())
                return $this->respondHttp500();

            if ($this->updateModelLocationForm($vehicle, $request->get('locations')))
                return $this->respondHttp500();

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se creo el vehiculo '.$vehicle->registration_number);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el vehiculo'
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
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->old_file_soat = $vehicle->file_soat;
            $vehicle->old_file_mechanical_tech = $vehicle->file_mechanical_tech;
            $vehicle->old_file_policy = $vehicle->file_policy;
            $vehicle->locations = $this->prepareDataLocationForm($vehicle);
            $vehicle->registration_number_date = $vehicle->registration_number_date ? (Carbon::createFromFormat('Y-m-d', $vehicle->registration_number_date))->format('D M d Y') : null;
            $vehicle->expedition_date_soat = $vehicle->expedition_date_soat ? (Carbon::createFromFormat('Y-m-d', $vehicle->expedition_date_soat))->format('D M d Y') : null;            
           //$vehicle->required_due_date_soat = $vehicle->required_due_date_soat;
            $vehicle->due_date_soat = $vehicle->due_date_soat ? (Carbon::createFromFormat('Y-m-d', $vehicle->due_date_soat))->format('D M d Y') : null;
            $vehicle->expedition_date_mechanical_tech = $vehicle->expedition_date_mechanical_tech ? (Carbon::createFromFormat('Y-m-d', $vehicle->expedition_date_mechanical_tech))->format('D M d Y') : null;
            //$vehicle->required_due_date_mechanical_tech = $vehicle->required_due_date_mechanical_tech;
            $vehicle->due_date_mechanical_tech = $vehicle->due_date_mechanical_tech ? (Carbon::createFromFormat('Y-m-d', $vehicle->due_date_mechanical_tech))->format('D M d Y') : null;
            $vehicle->expedition_date_policy = $vehicle->expedition_date_policy ? (Carbon::createFromFormat('Y-m-d', $vehicle->expedition_date_policy))->format('D M d Y') : null;
            //$vehicle->required_due_date_policy = $vehicle->required_due_date_policy;
            $vehicle->due_date_policy = $vehicle->due_date_policy ? (Carbon::createFromFormat('Y-m-d', $vehicle->due_date_policy))->format('D M d Y') : null;


            $vehicle->change_soat = '';
            $vehicle->change_mechanical = '';
            $vehicle->change_resposability = '';

            return $this->respondHttp200([
                'data' => $vehicle,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\VehicleRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        DB::beginTransaction();

        try
        {
            /*$plate = $this->tagsPrepare($request->get('plate'));
            $this->tagsSave($plate, TagsPlate::class);*/

            $name_propietary = $this->tagsPrepare($request->get('name_propietary'));
            $this->tagsSave($name_propietary, TagsNamePropietary::class);

            $type_vehicle = $this->tagsPrepare($request->get('type_vehicle'));
            $this->tagsSave($type_vehicle, TagsTypeVehicle::class);

            $mark = $this->tagsPrepare($request->get('mark'));
            $this->tagsSave($mark, TagsMark::class);

            $line = $this->tagsPrepare($request->get('line'));
            $this->tagsSave($line, TagsLine::class);

            $model = $this->tagsPrepare($request->get('model'));
            $this->tagsSave($model, TagsModel::class);

            $color = $this->tagsPrepare($request->get('color'));
            $this->tagsSave($color, TagsColor::class);

            $loading_capacity = $this->tagsPrepare($request->get('loading_capacity'));
            $this->tagsSave($loading_capacity, TagsCapacityLoading::class);

            ///General
            $vehicle->plate = $request->plate;//$plate->implode(',');
            $vehicle->name_propietary = $name_propietary->implode(',');
            $vehicle->registration_number = $request->registration_number;
            $vehicle->registration_number_date = $request->registration_number_date ? (Carbon::createFromFormat('D M d Y', $request->registration_number_date))->format('Y-m-d') : null;
            $vehicle->type_vehicle = $type_vehicle->implode(',');
            $vehicle->code_vehicle = $request->code_vehicle;

            ///Informacion matricula
            $vehicle->mark = $mark->implode(',');
            $vehicle->line = $line->implode(',');
            $vehicle->model = $model->implode(',');
            $vehicle->cylinder_capacity = $request->cylinder_capacity;
            $vehicle->color = $color->implode(',');
            $vehicle->chassis_number = $request->chassis_number;
            $vehicle->engine_number = $request->engine_number;
            $vehicle->passenger_capacity = $request->passenger_capacity;
            $vehicle->loading_capacity = $loading_capacity->implode(',');
            $vehicle->state = $request->state;

            ////SOAT
            $vehicle->soat_number = $request->soat_number;
            $vehicle->insurance = $request->insurance;
            $vehicle->expedition_date_soat = $request->expedition_date_soat ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_soat))->format('Y-m-d') : null;
            //$vehicle->required_due_date_soat = $request->required_due_date_soat;
            $vehicle->due_date_soat = $request->due_date_soat ? (Carbon::createFromFormat('D M d Y', $request->due_date_soat))->format('Y-m-d') : null;

            $path = "industrialSecure/roadSafety/files/".$this->company."/";

            if ($request->file_soat != $vehicle->file_soat)
            {
                $file = $request->file_soat;
                Storage::disk('s3')->delete($path.$vehicle->file_soat);
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->getClientOriginalExtension();
                $file->storeAs($path, $nameFile, 's3');
                $vehicle->file_soat = $nameFile;
            }

            //Tecno mecanica
            $vehicle->mechanical_tech_number = $request->mechanical_tech_number;
            $vehicle->issuing_entity = $request->issuing_entity;
            $vehicle->expedition_date_mechanical_tech = $request->expedition_date_mechanical_tech ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_mechanical_tech))->format('Y-m-d') : null;
            //$vehicle->required_due_date_mechanical_tech = $request->required_due_date_mechanical_tech;
            $vehicle->due_date_mechanical_tech = $request->due_date_mechanical_tech ? (Carbon::createFromFormat('D M d Y', $request->due_date_mechanical_tech))->format('Y-m-d') : null;

            if ($request->file_mechanical_tech != $vehicle->file_mechanical_tech)
            {
                $file_2 = $request->file_mechanical_tech;
                Storage::disk('s3')->delete($path.$vehicle->file_mechanical_tech);
                $nameFile_2 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_2->getClientOriginalExtension();
                $file_2->storeAs($path, $nameFile_2, 's3');
                $vehicle->file_mechanical_tech = $nameFile_2;
            }

            //Responsabilidad civil
            $vehicle->policy_responsability = $request->policy_responsability;
            $vehicle->policy_number = $request->policy_number;
            $vehicle->policy_entity = $request->policy_entity;
            $vehicle->expedition_date_policy = $request->expedition_date_policy ? (Carbon::createFromFormat('D M d Y', $request->expedition_date_policy))->format('Y-m-d') : null;
            //$vehicle->required_due_date_policy = $request->required_due_date_policy;
            $vehicle->due_date_policy = $request->due_date_policy ? (Carbon::createFromFormat('D M d Y', $request->due_date_policy))->format('Y-m-d') : null;


            if ($request->file_policy != $vehicle->file_policy)
            {
                $file_3 = $request->file_policy;
                Storage::disk('s3')->delete($path.$vehicle->file_policy);
                $nameFile_3 = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file_3->getClientOriginalExtension();
                $file_3->storeAs($path, $nameFile_3, 's3');
                $vehicle->file_policy = $nameFile_3;
            }

            if (!$vehicle->update())
                return $this->respondHttp500();

            if ($this->updateModelLocationForm($vehicle, $request->get('locations')))
                return $this->respondHttp500();

            if ($request->change_soat)
            {
                $vehicle->histories()->create([
                    'user_id' => $this->user->id,
                    'vehicle_id' => $vehicle->id,
                    'description' => $request->change_soat,
                    'type' => 'soat'
                ]);
            }

            if ($request->change_mechanical)
            {
                $vehicle->histories()->create([
                    'user_id' => $this->user->id,
                    'vehicle_id' => $vehicle->id,
                    'description' => $request->change_mechanical,
                    'type' => 'mechanical'
                ]);
            }

            if ($request->change_resposability)
            {
                $vehicle->histories()->create([
                    'user_id' => $this->user->id,
                    'vehicle_id' => $vehicle->id,
                    'description' => $request->change_resposability,
                    'type' => 'responsability'
                ]);
            }

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se edito el vehiculo '.$vehicle->registration_number);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el vehiculo'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        DB::beginTransaction();

        try
        {
            $file = $vehicle->file;

            $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se elimino el vehiculo '.$vehicle->registration_number);

            if (!$vehicle->delete())
            {
                return $this->respondHttp500();
            }

            DB::commit();

            $path = "industrialSecure/roadSafety/files/".$this->company."/";

            Storage::disk('s3')->delete($path.$file);
            
            return $this->respondHttp200([
                'message' => 'Se elimino el vehiculo'
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function downloadSoat(Vehicle $vehicle)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$vehicle->file_soat);
    }

    public function downloadMechanicalTech(Vehicle $vehicle)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$vehicle->file_mechanical_tech);
    }

    public function downloadPolicy(Vehicle $vehicle)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$vehicle->file_policy);
    }

    public function multiselectPlate(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsPlate::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectNamePropietary(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsNamePropietary::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectTypeVehicle(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsTypeVehicle::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
        else
        {
            $tags = TagsTypeVehicle::selectRaw("
                sau_rs_tag_type_vehicles.id as id,
                sau_rs_tag_type_vehicles.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('name', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectMark(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsMark::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectLine(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsLine::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectModel(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsModel::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectColor(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsColor::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    public function multiselectLoadingCapacity(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsCapacityLoading::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

   public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $vehicles = Vehicle::select("id", "plate as name")
                ->where('company_id', $this->company)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('plate', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($vehicles)
            ]);
        }
        else
        {
            $vehicles = Vehicle::selectRaw("
                sau_rs_vehicles.id as id,
                sau_rs_vehicles.plate as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($vehicles);
        }
    }
}
