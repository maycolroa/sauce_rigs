<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Drivers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverInfraction;
use App\Models\IndustrialSecure\RoadSafety\DriverInfractionType;
use App\Models\IndustrialSecure\RoadSafety\DriverInfractionTypeCode;
use App\Models\IndustrialSecure\RoadSafety\DriverInfractionFiles;
use App\Http\Requests\IndustrialSecure\RoadSafety\Drivers\InfractionRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class InfractionController extends Controller
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
        $infractions = DriverInfraction::selectRaw(
            'sau_rs_driver_infractions.*,
            sau_rs_infractions_type.name AS type,
            GROUP_CONCAT(CONCAT(" ", sau_rs_infractions_type_codes.code) ORDER BY sau_rs_infractions_type_codes.code ASC) as code,
            sau_rs_vehicles.plate as vehicle'
        )
        ->leftJoin('sau_rs_infractions_type', 'sau_rs_infractions_type.id', 'sau_rs_driver_infractions.type_id')
        ->leftJoin('sau_rs_driver_infractions_codes', 'sau_rs_driver_infractions_codes.infraction_id', 'sau_rs_driver_infractions.id')
        ->leftJoin('sau_rs_infractions_type_codes', 'sau_rs_infractions_type_codes.id', 'sau_rs_driver_infractions_codes.code_id')
        ->leftJoin('sau_rs_vehicles', 'sau_rs_vehicles.id', 'sau_rs_driver_infractions.vehicle_id')
        ->groupBy('sau_rs_driver_infractions.id')
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $infractions->where('sau_rs_driver_infractions.driver_id', '=', $request->get('modelId'));

        return Vuetable::of($infractions)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InfractionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InfractionRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $codes_types = $this->getValuesForMultiselect($request->codes_types);

            $infraction = new DriverInfraction;
            $infraction->company_id = $this->company;
            $infraction->driver_id = $request->driver_id;
            $infraction->type_id = $request->type_id;
            $infraction->vehicle_id = $request->vehicle_id;
            $infraction->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $infraction->date_simit = $request->date_simit ? (Carbon::createFromFormat('D M d Y', $request->date_simit))->format('Y-m-d') : null;


            if (!$infraction->save())
                return $this->respondHttp500();

            $detail_procedence = 'Seguridad vial - Infracciones'. $infraction->driver->employee->name. ', para el vehiculo: ' . $infraction->vehicle->plate;

            
            ActionPlan::user($this->user)
            ->module('roadSafety')
            ->url(url('/administrative/actionplans'))
            ->model($infraction)
            ->detailProcedence($detail_procedence)
            ->activities($request->actionPlan)
            ->save();

            $infraction->codesInfractions()->sync($codes_types);

            if ($request->has('evidences'))
                $this->saveFile($request->evidences, $infraction);

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Infracciones', 'Se creo la infraccion del conductor '.$infraction->driver->employee->name. ', para el vehiculo: ' . $infraction->vehicle->plate);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la infraccion'
        ]);
    }

    public function saveFile($files, $infraction)
    {
        if ($files && count($files) > 0)
        {
            $files_names_delete = [];

            foreach ($files as $keyF => $value) 
            {
                $create_file = true;

                if (isset($value['id']))
                {
                    $fileUpload = DriverInfractionFiles::findOrFail($value['id']);

                    if ($value['old_name'] == $value['file'] )
                            $create_file = false;
                    else
                        array_push($files_names_delete, $value['old_name']);
                }
                else
                {
                    $fileUpload = new DriverInfractionFiles();                    
                    $fileUpload->infraction_id = $infraction->id;
                    

                    $path = "industrialSecure/roadSafety/files/".$this->company."/";

                    $file_tmp = $value['file'];
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                    $file_tmp->storeAs($path, $nameFile, 's3');
                    $fileUpload->file = $nameFile;
                }

                if (!$fileUpload->save())
                    return $this->respondHttp500();
            }

            //Borrar archivos reemplazados
            foreach ($files_names_delete as $keyf => $file)
            {
                Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
            }
        }
    }

    public function getFiles($infraction)
    {
        $get_files = DriverInfractionFiles::where('infraction_id', $infraction)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->file = $get_file->file;
                $get_file->old_name = $get_file->file;

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
            $infraction = DriverInfraction::findOrFail($id);
            $infraction->date = $infraction->date ? (Carbon::createFromFormat('Y-m-d', $infraction->date))->format('D M d Y') : null;
            $infraction->date_simit = $infraction->date_simit ? (Carbon::createFromFormat('Y-m-d', $infraction->date_simit))->format('D M d Y') : null;

            $codes = [];

            $infraction->multiselect_vehicle = $infraction->vehicle->multiselect();
            $infraction->multiselect_type = $infraction->typeInfraction ? $infraction->typeInfraction->multiselect() : [];

            $infraction->evidences = $this->getFiles($infraction->id);

            foreach ($infraction->codesInfractions as $key => $value)
            {                
                array_push($codes, $value->multiselect());
            }

            $infraction->multiselect_code_type = $codes;
            $infraction->codes_types = $codes;


            $infraction->actionPlan = ActionPlan::model($infraction)->prepareDataComponent();

            $infraction->delete = [
                'files' => []
            ];

            return $this->respondHttp200([
                'data' => $infraction,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\InfractionRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(InfractionRequest $request, DriverInfraction $driverInfraction)
    {
        DB::beginTransaction();

        try
        {
            $codes_types = $this->getValuesForMultiselect($request->codes_types);

            $driverInfraction->type_id = $request->type_id;
            $driverInfraction->vehicle_id = $request->vehicle_id;
            $driverInfraction->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $driverInfraction->date_simit = $request->date_simit ? (Carbon::createFromFormat('D M d Y', $request->date_simit))->format('Y-m-d') : null;

            if(!$driverInfraction->update()){
                return $this->respondHttp500();
            }

            $detail_procedence = 'Seguridad vial - Infracciones'. $driverInfraction->driver->employee->name. ', para el vehiculo: ' . $driverInfraction->vehicle->plate;

            
            ActionPlan::user($this->user)
            ->module('roadSafety')
            ->url(url('/administrative/actionplans'))
            ->model($driverInfraction)
            ->detailProcedence($detail_procedence)
            ->activities($request->actionPlan)
            ->save();

            $driverInfraction->codesInfractions()->sync($codes_types);

            if ($request->has('evidences'))
                $this->saveFile($request->evidences, $driverInfraction);

            $this->deleteData($request->get('delete'));

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Connductores', 'Se edito la infraccion del conductor '.$driverInfraction->driver->employee->name. ', para el vehiculo: ' . $driverInfraction->vehicle->plate);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la infracción'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(DriverInfraction $driverInfraction)
    {
        $fileBk = $driverInfraction->replicate();

        $file_delete = DriverInfractionFiles::where('infraction_id', $driverInfraction->id)->get();

        $path = "industrialSecure/roadSafety/files/".$this->company."/";

        if ($file_delete)
        {
            foreach ($file_delete as $keyf => $file)
            {
                $file2 = $file->file;
                Storage::disk('s3')->delete($path.$file2);
            }
        }

        $this->saveLogActivitySystem('Seguridad vial - Infracciones', 'Se elimino la infraccion del conductor '.$driverInfraction->driver->employee->name. ', para el vehiculo: ' . $driverInfraction->vehicle->plate);

        if (!$vehiclesMaintenance->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la infraccion'
        ]);
    }

    private function deleteData($data)
    {    
        $path = "industrialSecure/roadSafety/files/".$this->company."/";

        foreach ($data['files'] as $keyF => $file)
        {
            $file_delete = DriverInfractionFiles::find($file);

            if ($file_delete)
            {                
                Storage::disk('s3')->delete($path.$file_delete->file);
                $file_delete->delete();
            }
        }
    }

    public function multiselectType(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = DriverInfractionType::select("id", "name")
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

    public function multiselectTypeCode(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $codes = DriverInfractionTypeCode::selectRaw(
                "sau_rs_infractions_type_codes.id as id,
                sau_rs_infractions_type_codes.code as name")
            ->join('sau_rs_infractions_type', 'sau_rs_infractions_type.id', 'sau_rs_infractions_type_codes.type_id')
            ->where(function ($query) use ($keyword) {
                $query->orWhere('sau_rs_infractions_type_codes.code', 'like', $keyword);
            });

            if ($request->has('type_id') && $request->get('type_id') != '')
            {
                $regional = $request->get('type_id');
                
                if (is_numeric($regional))
                    $codes->where('type_id', $request->get('type_id'));
                else
                {
                    $type_select = $this->getValuesForMultiselect($type_id);

                    $codes->whereIn('type_id', $type_selects);
                }
            }

            $codes = $codes->orderBy('id')->take(30)->get();                
            $codes = $codes->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($codes)
            ]);
        }
    }


    public function downloadFile(DriverInfractionFiles $driverInfraction)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$driverInfraction->file);
    }
}
