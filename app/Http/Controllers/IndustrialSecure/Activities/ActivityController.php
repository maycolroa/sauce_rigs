<?php

namespace App\Http\Controllers\IndustrialSecure\Activities;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Activities\Activity;
use App\Http\Requests\IndustrialSecure\Activities\ActivityRequest;

class ActivityController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:activities_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:activities_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:activities_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:activities_d, {$this->team}", ['only' => 'destroy']);
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
        $activities = Activity::select('*');

        return Vuetable::of($activities)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityRequest $request)
    {
        $activity = new Activity($request->all());
        $activity->company_id = $this->company;
        
        if(!$activity->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - Actividades', 'Se creo la actividad '.$activity->name.' ');

        return $this->respondHttp200([
            'message' => 'Se creo la actividad'
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
            $activity = Activity::findOrFail($id);

            return $this->respondHttp200([
                'data' => $activity,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $activity->fill($request->all());
        
        if(!$activity->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - Actividades', 'Se edito la actividad '.$activity->name.' ');
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la actividad'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        if (count($activity->dangerMatrices) > 0)
        {
            return $this->respondWithError('No se puede eliminar la actividad porque hay matrices de peligro asociadas a ella');
        }

        if(!$activity->delete())
        {
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - Actividades', 'Se elimino la actividad '.$activity->name.' ');
        
        return $this->respondHttp200([
            'message' => 'Se elimino la actividad'
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
            $activities = Activity::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = Activity::selectRaw("
                sau_dm_activities.id as id,
                sau_dm_activities.name as name
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }
}
