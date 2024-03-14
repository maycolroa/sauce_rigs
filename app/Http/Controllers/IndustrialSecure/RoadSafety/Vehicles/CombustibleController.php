<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Combustible;
use App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles\CombustibleRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class CombustibleController extends Controller
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
        $combustible = Combustible::select(
            'sau_rs_vehicle_combustibles.*',
            'sau_employees.name as driver'
        )
        ->join('sau_rs_drivers', 'sau_rs_drivers.id', 'sau_rs_vehicle_combustibles.driver_id')
        ->join('sau_employees', 'sau_employees.id', 'sau_rs_drivers.employee_id')
        ->orderBy('id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $combustible->where('sau_rs_vehicle_combustibles.vehicle_id', '=', $request->get('modelId'));

        return Vuetable::of($combustible)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CombustibleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CombustibleRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $combustible = new Combustible;
            $combustible->vehicle_id = $request->vehicle_id;

            ///General
            $combustible->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $combustible->driver_id = $request->driver_id;
            $combustible->km = $request->km;
            $combustible->cylinder_capacity = $request->cylinder_capacity;
            $combustible->quantity_galons = $request->quantity_galons;
            $combustible->price_galon = $request->price_galon;

            if (!$combustible->save())
                return $this->respondHttp500();

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se creo la caarga de combustible con fecha'.$combustible->date.' al vehiculo con id '.$combustible->vehicle_id);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la carga de combustible'
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
            $combustible = Combustible::findOrFail($id);
            $combustible->date = $combustible->date ? (Carbon::createFromFormat('Y-m-d', $combustible->date))->format('D M d Y') : null;
            $combustible->multiselect_driver = $combustible->driver->multiselect();

            \Log::info($combustible);

            return $this->respondHttp200([
                'data' => $combustible,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\CombustibleRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(CombustibleRequest $request, Combustible $vehiclesCombustible)
    {
        DB::beginTransaction();

        try
        {
            ///General
            $vehiclesCombustible->date = $request->date ? (Carbon::createFromFormat('D M d Y', $request->date))->format('Y-m-d') : null;
            $vehiclesCombustible->driver_id = $request->driver_id;
            $vehiclesCombustible->km = $request->km;
            $vehiclesCombustible->cylinder_capacity = $request->cylinder_capacity;
            $vehiclesCombustible->quantity_galons = $request->quantity_galons;
            $vehiclesCombustible->price_galon = $request->price_galon;

            if (!$vehiclesCombustible->update())
                return $this->respondHttp500();

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se edito la caarga de combustible con fecha'.$vehiclesCombustible->date.' al vehiculo con id '.$vehiclesCombustible->vehicle_id);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la carga de combustible'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */

    public function destroy(Combustible $vehiclesCombustible)
    {
        $this->saveLogActivitySystem('Seguridad vial - Vehiculos', 'Se creo la carga de combustible con fecha'.$vehiclesCombustible->date.' al vehiculo con id '.$vehiclesCombustible->vehicle_id);

        if (!$vehiclesCombustible->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la carga de combustible'
        ]);
    }
}
