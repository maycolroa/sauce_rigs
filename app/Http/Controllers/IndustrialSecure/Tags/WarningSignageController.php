<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class WarningSignageController extends Controller
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
        $warning_signage = TagsWarningSignage::select('*');

        return Vuetable::of($warning_signage)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsWarningSignage($request->all());
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles Señalizacion y advertencias', 'Se creo el tag '.$tag->name.' ');

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
            $tag = TagsWarningSignage::findOrFail($id);
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
    public function update(Request $request, TagsWarningSignage $warningSignage)
    {
        if ($request->rewrite)
        {
            $name_old = $warningSignage->name;
            $warningSignage->fill($request->all());
            
            if(!$warningSignage->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $warningSignage->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Controles Señalizacion y advertencias', 'Se edito el tag '.$warningSignage->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsWarningSignage::find($request->replace_deleted);

            $this->rewriteTag($warningSignage->name, $new_tag->name);
            $this->destroy($warningSignage);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($warningSignage->name, '');
            $this->destroy($warningSignage);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $existing_controls_warning_signage_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.existing_controls_warning_signage
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.existing_controls_warning_signage', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($existing_controls_warning_signage_data as $key => $value) 
        {
           $value->existing_controls_warning_signage = str_replace($old_name, $new_name, $value->existing_controls_warning_signage);

           $value->save();
        }

        $intervention_measures_warning_signage_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.intervention_measures_warning_signage
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.intervention_measures_warning_signage', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($intervention_measures_warning_signage_data as $key => $value) 
        {
           $value->intervention_measures_warning_signage = str_replace($old_name, $new_name, $value->intervention_measures_warning_signage);

           $value->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsWarningSignage  $warningSignage
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsWarningSignage $warningSignage)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles Señalizacion y advertencias', 'Se elimino el tag '.$warningSignage->name.' ');

        if(!$warningSignage->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $existing_controls_warning_signage = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - Señalizacion y Advertencias' campo,  
            sau_dm_activity_danger.existing_controls_warning_signage as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dm_activity_danger.existing_controls_warning_signage', 'like', "%$request->keyword%");


        $intervention_measures_warning_signage = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Medidas de prevencion - Señalizacion y Advertencias' campo,  
            sau_dm_activity_danger.intervention_measures_warning_signage as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dm_activity_danger.intervention_measures_warning_signage', 'like', "%$request->keyword%");

        $existing_controls_warning_signage->union($intervention_measures_warning_signage);

        return Vuetable::of($existing_controls_warning_signage
        )
                    ->make();

    }
}
