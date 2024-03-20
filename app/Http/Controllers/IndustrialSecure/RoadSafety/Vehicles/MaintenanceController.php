<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Maintenance;
use App\Models\IndustrialSecure\RoadSafety\MaintenanceFiles;
use App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class MaintenanceController extends Controller
{
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
        $maintenances = Maintenance::select(
            'sau_rs_vehicle_maintenance.*'
        )
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $maintenances->where('sau_rs_vehicle_maintenance.vehicle_id', '=', $request->get('modelId'));

        return Vuetable::of($maintenances)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\MaintenanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaintenanceRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $maintenance = new Maintenance;
            $maintenance->vehicle_id = $request->vehicle_id;

            ///General
            $maintenance->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $maintenance->type = $request->type;
            $maintenance->km = $request->km;
            $maintenance->description = $request->description;
            $maintenance->responsible = $request->responsible;
            $maintenance->apto = $request->apto;
            $maintenance->reason = $request->reason;
            $maintenance->next_date = $request->next_date ? (Carbon::createFromFormat('D M d Y', $request->next_date))->format('Y-m-d') : null;

            if (!$maintenance->save())
                return $this->respondHttp500();

            $this->saveFile($maintenance, $request->get('evidences'));

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se creo el mantenimiento con fecha'.$maintenance->date.' al vehiculo con id '.$maintenance->vehicle_id);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el mantenimiento'
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
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->date = $maintenance->date ? (Carbon::createFromFormat('Y-m-d', $maintenance->date))->format('D M d Y') : null;
            $maintenance->next_date = $maintenance->next_date ? (Carbon::createFromFormat('Y-m-d', $maintenance->next_date))->format('D M d Y') : null;


            $maintenance->evidences = $this->getFiles($maintenance->id);

            return $this->respondHttp200([
                'data' => $maintenance,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\MaintenanceRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(MaintenanceRequest $request, Maintenance $vehiclesMaintenance)
    {
        DB::beginTransaction();

        try
        {
            $vehiclesMaintenance->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $vehiclesMaintenance->type = $request->type;
            $vehiclesMaintenance->km = $request->km;
            $vehiclesMaintenance->description = $request->description;
            $vehiclesMaintenance->responsible = $request->responsible;
            $vehiclesMaintenance->apto = $request->apto;
            $vehiclesMaintenance->reason = $request->reason;
            $vehiclesMaintenance->next_date = $request->next_date ? (Carbon::createFromFormat('D M d Y', $request->next_date))->format('Y-m-d') : null;

            if (!$vehiclesMaintenance->update())
                return $this->respondHttp500();

            $this->saveFile($vehiclesMaintenance, $request->get('evidences'));

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se edito el mantenimiento con fecha'.$vehiclesMaintenance->date.' al vehiculo con id '.$vehiclesMaintenance->vehicle_id);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el mantenimiento'
        ]);
    }

    public function saveFile($maintenance, $files)
    {
        if ($files && count($files) > 0)
        {
            $files_names_delete = [];

            foreach ($files as $keyF => $value) 
            {
                $create_file = true;

                if (isset($value['id']))
                {
                    $fileUpload = MaintenanceFiles::findOrFail($value['id']);

                    if ($value['old_name'] == $value['file'] )
                            $create_file = false;
                    else
                        array_push($files_names_delete, $value['old_name']);
                }
                else
                {
                    $fileUpload = new MaintenanceFiles();                    
                    $fileUpload->vehicle_maintenance_id = $maintenance->id;
                    

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

    public function getFiles($maintenance)
    {
        $get_files = MaintenanceFiles::where('vehicle_maintenance_id', $maintenance)->get();

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
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */

    public function destroy(Maintenance $vehiclesMaintenance)
    {
        $fileBk = $vehiclesMaintenance->replicate();

        $file_delete = MaintenanceFiles::where('vehicle_maintenance_id', $vehiclesMaintenance->id)->get();

        $path = "industrialSecure/roadSafety/files/".$this->company."/";

        if ($file_delete)
        {
            foreach ($file_delete as $keyf => $file)
            {
                $file2 = $file->file;
                Storage::disk('s3')->delete($path.$file2);
            }
        }

        $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se creo el mantenimiento con fecha'.$vehiclesMaintenance->date.' al vehiculo con id '.$vehiclesMaintenance->vehicle_id);

        if (!$vehiclesMaintenance->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino el mantenimiento'
        ]);
    }


    public function downloadFile(MaintenanceFiles $maintenanceFiles)
    {
        $path = "industrialSecure/roadSafety/files/".$this->company."/";
        return Storage::disk('s3')->download($path.$maintenanceFiles->file);
    }
}
