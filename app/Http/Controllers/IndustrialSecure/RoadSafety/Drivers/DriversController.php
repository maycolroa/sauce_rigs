<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Drivers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverDocument;
use App\Models\IndustrialSecure\RoadSafety\Position;
use App\Models\IndustrialSecure\RoadSafety\PositionDocument;
use App\Models\IndustrialSecure\RoadSafety\TagsTypeLicense;
use App\Models\IndustrialSecure\RoadSafety\TagsResponsibles;
use App\Http\Requests\IndustrialSecure\RoadSafety\Drivers\DriverRequest;
use App\Models\Administrative\Positions\EmployeePosition;
use Illuminate\Support\Facades\Storage;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use DB;

class DriversController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:roadsafety_drivers_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:roadsafety_drivers_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:roadsafety_drivers_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:roadsafety_drivers_d, {$this->team}", ['only' => 'destroy']);
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
        $drivers = Driver::selectRaw(
            'sau_rs_drivers.*,
            sau_rs_tag_type_license.name AS type_license,
            sau_employees.name,
            GROUP_CONCAT(CONCAT(" ", sau_rs_vehicles.plate) ORDER BY sau_rs_vehicles.plate ASC) as registration_number,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name as headquarter,
            sau_employees_processes.name as process,
            sau_employees_areas.name as area'
        )
        ->join('sau_employees', 'sau_employees.id', 'sau_rs_drivers.employee_id')
        ->leftJoin('sau_rs_tag_type_license', 'sau_rs_tag_type_license.id', 'sau_rs_drivers.type_license_id')
        ->leftJoin('sau_rs_driver_vehicles', 'sau_rs_driver_vehicles.driver_id', 'sau_rs_drivers.id')
        ->leftJoin('sau_rs_vehicles', 'sau_rs_vehicles.id', 'sau_rs_driver_vehicles.vehicle_id')
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_employees.employee_headquarter_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_employees.employee_process_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_employees.employee_area_id')
        ->groupBy('sau_rs_drivers.id')
        ->where('sau_employees.company_id', $this->company)
        ->orderBy('id', 'DESC');

        $url = "/industrialsecure/roadsafety/drivers";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            if (isset($filters["typeLicense"]))
                $drivers->inTypeLicenses($this->getValuesForMultiselect($filters["typeLicense"]), $filters['filtersType']['typeLicense']);

            if (isset($filters["regionals"]))
                $drivers->inRegionals($this->getValuesForMultiselect($filters["regionals"]), $filters['filtersType']['regionals']);

            if (isset($filters["headquarters"]))
                $drivers->inHeadquarters($this->getValuesForMultiselect($filters["headquarters"]), $filters['filtersType']['headquarters']);

            if (isset($filters["processes"]))
                $drivers->inProcesses($this->getValuesForMultiselect($filters["processes"]), $filters['filtersType']['processes']);
            
            if (isset($filters["areas"]))
                $drivers->inAreas($this->getValuesForMultiselect($filters["areas"]), $filters['filtersType']['areas']);
        }

        $dates_request = isset($filters["dateRange"]) ? explode('/', $filters["dateRange"]) : [];

        $dates = [];

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, $this->formatDateToSave($dates_request[0]));
            array_push($dates, $this->formatDateToSave($dates_request[1]));
        }
            
        $drivers->betweenDate($dates);

        return Vuetable::of($drivers)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\DriverRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverRequest $request)
    {
        DB::beginTransaction();

        try
        {            
            $responsible = $this->tagsPrepare($request->get('responsible'));
            $this->tagsSave($responsible, TagsResponsibles::class);

            $vehicles = $this->getValuesForMultiselect($request->vehicle_id);

            $driver = new Driver;
            $driver->employee_id = $request->employee_id;
            $driver->responsible = $responsible->implode(',');
            $driver->type_license_id = $request->type_license_id;
            $driver->date_license = $request->date_license ? (Carbon::createFromFormat('D M d Y', $request->date_license))->format('Y-m-d') : null;

            if (!$driver->save())
                return $this->respondHttp500();

            $driver->vehicles()->sync($vehicles);

            if ($request->has('documents'))
                $this->saveFile($request->documents, $driver);

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Connductores', 'Se creo el conductor '.$driver->employee->name);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el conductor'
        ]);
    }

    public function saveFile($files, $driver)
    {
        if ($files && count($files) > 0)
        {
            foreach ($files as $keyF => $value) 
            {
                if (isset($value['file']) && $value['file'])
                {
                    $create_file = true;

                    if (isset($value['id']))
                    {
                        $fileUpload = DriverDocument::findOrFail($value['id']);
                        $fileUpload->position_document_id = $value['position_document_id'];
                        $fileUpload->required_expiration_date = $value['required_expiration_date'];
                        $fileUpload->expiration_date = $value['expiration_date'] ? (Carbon::createFromFormat('D M d Y', $value['expiration_date']))->format('Y-m-d') : NULL;

                        if ($value['old_name'] == $value['file'] )
                            $create_file = false;
                    }
                    else
                    {
                        $fileUpload = new DriverDocument();                    
                        $fileUpload->driver_id = $driver->id;
                        $fileUpload->name = $value['name'];
                        $fileUpload->position_document_id = $value['position_document_id'];
                        $fileUpload->required_expiration_date = $value['required_expiration_date'];
                        $fileUpload->expiration_date = $value['expiration_date'] ? (Carbon::createFromFormat('D M d Y', $value['expiration_date']))->format('Y-m-d') : NULL;
                    }

                    if ($create_file)
                    {
                        $path = "industrialSecure/roadSafety/files/".$this->company."/";

                        $file_tmp = $value['file'];
                        $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->getClientOriginalExtension();
                       // $file_tmp->storeAs($path, $nameFile, 's3');
                        $fileUpload->file = $nameFile;
                    }

                    if (!$fileUpload->save())
                        return $this->respondHttp500();
                }
            }
        }
        
    }

    public function getFiles($driver)
    {
        $files = [];

        $position = Position::where('employee_position_id', $driver->employee->employee_position_id)->first();

        if ($position)
        {
            $documents = PositionDocument::where('position_id', $position->id)->get();

            if ($documents->count() > 0)
            {
                foreach ($documents as $key => $document) 
                {
                    $get_file = DriverDocument::where('driver_id', $driver->id)->where('position_document_id', $document->id)->orderBy('id', 'DESC')->first();

                    if ($get_file)
                    {
                        $content = [
                            'id' => $get_file->id,
                            'key' => Carbon::now()->timestamp + rand(1,10000),
                            'name' => $get_file->name,
                            'old_name' => $get_file->file,
                            'position_document_id' => $document->id,
                            'file' => $get_file->file,                            
                            'required_expiration_date' => $get_file->required_expiration_date,
                            'expiration_date' => $get_file->expiration_date ? (Carbon::createFromFormat('Y-m-d', $get_file->expiration_date))->format('D M d Y') : NULL
                        ];
                    }
                    else
                    {
                        $content = [
                            'key' => Carbon::now()->timestamp + rand(1,10000),
                            'name' => $document->name,
                            'position_document_id' => $document->id,
                            'file' => '',
                            'required_expiration_date' => 'SI',
                            'expiration_date' => ''
                        ];
                    }

                    array_push($files, $content);
                }
            }
        }

        return $files;
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
            $driver = Driver::findOrFail($id);
            $driver->date_license = $driver->date_license ? (Carbon::createFromFormat('Y-m-d', $driver->date_license))->format('D M d Y') : null;

            $vehicles = [];

            $driver->multiselect_employee = $driver->employee->multiselect();
            $driver->multiselect_type_license = $driver->typeLicense ? $driver->typeLicense->multiselect() : [];
            //$driver->multiselect_responsible = $driver->responsible && $driver->responsible->multiselect() ? $driver->responsible->multiselect() : [];

            $driver->documents = $this->getFiles($driver);

            foreach ($driver->vehicles as $key => $value)
            {                
                array_push($vehicles, $value->multiselect());
            }

            $driver->multiselect_vehicle = $vehicles;
            $driver->vehicle_id = $vehicles;

            return $this->respondHttp200([
                'data' => $driver,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\DriverRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(DriverRequest $request, Driver $driver)
    {
        DB::beginTransaction();

        try
        {
            $responsible = $this->tagsPrepare($request->get('responsible'));
            $this->tagsSave($responsible, TagsResponsibles::class);
            $vehicles = $this->getValuesForMultiselect($request->vehicle_id);

            $driver->employee_id = $request->employee_id;
            $driver->responsible = $responsible->implode(',');
            $driver->type_license_id = $request->type_license_id;
            $driver->date_license = $request->date_license ? (Carbon::createFromFormat('D M d Y', $request->date_license))->format('Y-m-d') : null;

            if(!$driver->update()){
                return $this->respondHttp500();
            }

            $driver->vehicles()->sync($vehicles);

            if ($request->has('documents'))
                $this->saveFile($request->documents, $driver);

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Conductores', 'Se edito el conductor '.$driver->employee->name);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el conductor'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        $this->saveLogActivitySystem('Seguridad vial - Conductores', 'Se elimino el conductor '.$driver->employee->name);

        if (!$driver->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el condutor'
        ]);
    }

    private function deleteData($data)
    {    
        if (COUNT($data['documents']) > 0)
            Position::destroy($data['documents']);
    }

    public function getDocuments(Request $request)
    {
        $files = [];

        $position = Position::where('employee_position_id', $request->position_id)->first();

        if ($position)
        {
            $documents = PositionDocument::where('position_id', $position->id)->get();

            if ($documents->count() > 0)
            {
                foreach ($documents as $key => $document) 
                {
                    $content = [
                        'key' => Carbon::now()->timestamp + rand(1,10000),
                        'name' => $document->name,
                        'position_document_id' => $document->id,
                        'file' => '',
                        'required_expiration_date' => 'SI',
                        'expiration_date' => ''
                    ];

                    array_push($files, $content);
                }
            }
        }

        return $this->respondHttp200([
            'data' => $files
        ]);
    }

    public function multiselectTypeLicense(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsTypeLicense::select("id", "name")
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
            $tags = TagsTypeLicense::selectRaw("
                sau_rs_tag_type_license.id as id,
                sau_rs_tag_type_license.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($tags);
        }
    }

    public function multiselectResponsibles(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagsResponsibles::select("id", "name")
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
            $tags = TagsResponsibles::selectRaw("
                sau_rs_tag_responsibles.id as id,
                sau_rs_tag_responsibles.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($tags);
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
            $drivers = Driver::select(
                "sau_rs_drivers.id as id", 
                "sau_employees.name as name"
            )
            ->join('sau_employees', 'sau_employees.id', 'sau_rs_drivers.employee_id')
            ->where('sau_employees.company_id', $this->company)
            ->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', $keyword);
            })
            ->orderBy('name')
            ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($drivers)
            ]);
        }
        else
        {
            $drivers = Driver::selectRaw("
                sau_rs_drivers.id as id,
                sau_employees.name as name
            ")
            ->join('sau_employees', 'sau_employees.id', 'sau_rs_drivers.employee_id')
            ->where('sau_employees.company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($drivers);
        }
    }

    public function downloadFile(DriverDocument $driverDocument)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$driverDocument->file);
    }
}
