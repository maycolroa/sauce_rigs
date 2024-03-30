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
use App\Http\Requests\IndustrialSecure\RoadSafety\Drivers\DriverRequest;
use App\Models\Administrative\Positions\EmployeePosition;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class DriversController extends Controller
{
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
        ->orderBy('id', 'DESC');

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
            $vehicles = $this->getValuesForMultiselect($request->vehicle_id);

            $driver = new Driver;
            $driver->employee_id = $request->employee_id;
            $driver->responsible_id = $request->responsible_id;
            //$driver->vehicle_id = $request->vehicle_id 
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
                        $fileUpload->expiration_date = (Carbon::createFromFormat('D M d Y', $value['expiration_date']))->format('Y-m-d');

                        if ($value['old_name'] == $value['file'] )
                            $create_file = false;
                    }
                    else
                    {
                        $fileUpload = new DriverDocument();                    
                        $fileUpload->driver_id = $driver->id;
                        $fileUpload->name = $value['name'];
                        $fileUpload->expiration_date = (Carbon::createFromFormat('D M d Y', $value['expiration_date']))->format('Y-m-d');
                    }

                    if ($create_file)
                    {
                        $path = "industrialSecure/roadSafety/files/".$this->company."/";

                        $file_tmp = $value['file'];
                        $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                        $file_tmp->storeAs($path, $nameFile, 's3');
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
        $get_files = DriverDocument::where('driver_id', $driver)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->id = $get_file->id;
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->name = $get_file->name;
                $get_file->old_name = $get_file->file;
                $get_file->expiration_date = $get_file->expiration_date ? (Carbon::createFromFormat('Y-m-d', $get_file->expiration_date))->format('D M d Y') : null;;

                return $get_file;
            });

            $files = $get_files;
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
            $driver->multiselect_responsible = $driver->responsible->multiselect();

            $driver->documents = $this->getFiles($driver->id);

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
            $vehicles = $this->getValuesForMultiselect($request->vehicle_id);

            $driver->employee_id = $request->employee_id;
            $driver->responsible_id = $request->responsible_id;
            //$driver->vehicle_id = $request->vehicle_id;
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
                        'file' => '',
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
