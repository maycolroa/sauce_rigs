<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Location;
use App\Http\Requests\IndustrialSecure\Epp\LocationRequest;

class LocationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:location_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:location_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:location_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:location_d, {$this->team}", ['only' => 'destroy']);
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
        $elements = Location::selectRaw("
            sau_epp_locations.*,
            sau_epp_locations.name as name,
            sau_employees_regionals.name as regional,
            sau_employees_headquarters.name as headquarter,
            sau_employees_areas.name as area,
            sau_employees_processes.name as process"
        )
        ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_epp_locations.employee_regional_id')
        ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_epp_locations.employee_headquarter_id')
        ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_epp_locations.employee_area_id')
        ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_epp_locations.employee_process_id');

        return Vuetable::of($elements)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\LocationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $request)
    {

        $location = new Location();
        $location->name = $request->name;
        $location->company_id = $this->company;
        
        if(!$location->save())
            return $this->respondHttp500();

        if($this->updateModelLocationForm($location, $request->get('locations')))
        {
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la ubicación'
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
            $location = Location::findOrFail($id);

            $location->locations = $this->prepareDataLocationForm($location);

            return $this->respondHttp200([
                'data' => $location,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\elements\LocationRequest  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, Location $location)
    {
        $location->name = $request->name;
        
        if(!$location->update()){
          return $this->respondHttp500();
        }

        if($this->updateModelLocationForm($location, $request->get('locations')))
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la ubicación'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {

        if(!$location->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la ubicación'
        ]);
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
            $locations = Location::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($locations)
            ]);
        }
        else
        {
            $locations = Location::selectRaw("
                sau_epp_locations.id as id,
                sau_epp_locations.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($locations);
        }
    }
}
