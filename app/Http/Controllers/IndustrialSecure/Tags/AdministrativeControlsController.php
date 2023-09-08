<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsAdministrativeControls;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class AdministrativeControlsController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:dangerMatrix_c, {$this->team}");
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
        $controls = TagsAdministrativeControls::select('*');

        return Vuetable::of($controls)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsAdministrativeControls($request->all());
        $tag->name = trim(str_replace(',', '', $request->name));
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles administrativos', 'Se creo el tag '.$tag->name.' ');

        return $this->respondHttp200([
            'message' => 'Se creo el tag'
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
            $tag = TagsAdministrativeControls::findOrFail($id);
            $tag->rewrite = '';
            $tag->replace = '';
            $tag->replace_deleted = '';

            return $this->respondHttp200([
                'data' => $tag,
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
    public function update(Request $request, TagsAdministrativeControls $administrativeControl)
    {
        if ($request->rewrite)
        {
            $name_old = $administrativeControl->name;
            $administrativeControl->fill($request->all());
            $administrativeControl->name = trim(str_replace(',', '', $request->name));
            
            if(!$administrativeControl->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $administrativeControl->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Controles administrativos', 'Se edito el tag '.$administrativeControl->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsAdministrativeControls::find($request->replace_deleted);

            $this->rewriteTag($administrativeControl->name, $new_tag->name);
            $this->destroy($administrativeControl);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($administrativeControl->name, '');
            $this->destroy($administrativeControl);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $existing_controls_administrative_controls_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.existing_controls_administrative_controls
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.existing_controls_administrative_controls', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($existing_controls_administrative_controls_data as $key => $value) 
        {
            if ($value->existing_controls_administrative_controls)
            {
                $controls = explode(',', $value->existing_controls_administrative_controls);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->existing_controls_administrative_controls = $controls;
                $value->save();
            }
        }

        $intervention_measures_administrative_controls_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.intervention_measures_administrative_controls
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.intervention_measures_administrative_controls', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($intervention_measures_administrative_controls_data as $key => $value) 
        {
            if ($value->intervention_measures_administrative_controls)
            {
                $controls = explode(',', $value->intervention_measures_administrative_controls);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->intervention_measures_administrative_controls = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsAdministrativeControls  $administrativeControl
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsAdministrativeControls $administrativeControl)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles administrativos', 'Se elimino el tag '.$administrativeControl->name.' ');

        if(!$administrativeControl->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $existing_controls_administrative_controls = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - Controles Administrativos' campo,  
            sau_dm_activity_danger.existing_controls_administrative_controls as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.existing_controls_administrative_controls) > 0");


        $intervention_measures_administrative_controls = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Medidas de prevencion - Controles administrativos' campo,  
            sau_dm_activity_danger.intervention_measures_administrative_controls as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.intervention_measures_administrative_controls) > 0");

        $existing_controls_administrative_controls->union($intervention_measures_administrative_controls);

        return Vuetable::of($existing_controls_administrative_controls
        )->make();

    }
}
