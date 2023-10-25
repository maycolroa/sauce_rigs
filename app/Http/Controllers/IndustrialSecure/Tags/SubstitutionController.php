<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsSubstitution;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class SubstitutionController extends Controller
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
        $substitutions = TagsSubstitution::select('*')->orderBy('id', 'DESC');

        return Vuetable::of($substitutions)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsSubstitution($request->all());
        $tag->name = trim(str_replace(',', '', $request->name));
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles Sustituciones', 'Se creo el tag '.$tag->name.' ');

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
            $tag = TagsSubstitution::findOrFail($id);
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
    public function update(Request $request, TagsSubstitution $substitution)
    {
        if ($request->rewrite)
        {
            $name_old = $substitution->name;
            $substitution->fill($request->all());
            $substitution->name = trim(str_replace(',', '', $request->name));
            
            if(!$substitution->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $substitution->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Controles sustitucion', 'Se edito el tag '.$substitution->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsSubstitution::find($request->replace_deleted);

            $this->rewriteTag($substitution->name, $new_tag->name);
            $this->destroy($substitution);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($substitution->name, '');
            $this->destroy($substitution);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $existing_controls_substitution_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.existing_controls_substitution
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.existing_controls_substitution', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($existing_controls_substitution_data as $key => $value) 
        {
           if ($value->existing_controls_substitution)
            {
                $controls = explode(',', $value->existing_controls_substitution);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->existing_controls_substitution = $controls;
                $value->save();
            }
        }

        $intervention_measures_substitution_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.intervention_measures_substitution
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.intervention_measures_substitution', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($intervention_measures_substitution_data as $key => $value) 
        {
           if ($value->intervention_measures_substitution)
            {
                $controls = explode(',', $value->intervention_measures_substitution);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->intervention_measures_substitution = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsSubstitution  $substitution
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsSubstitution $substitution)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles sustitucion', 'Se elimino el tag '.$substitution->name.' ');

        if(!$substitution->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $existing_controls_substitution = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - Sustituciones' campo,  
            sau_dm_activity_danger.existing_controls_substitution as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.existing_controls_substitution) > 0");


        $intervention_measures_substitution = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Medidas de prevencion -  Sustituciones' campo,  
            sau_dm_activity_danger.intervention_measures_substitution as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.intervention_measures_substitution) > 0");

        $existing_controls_substitution->union($intervention_measures_substitution);

        return Vuetable::of($existing_controls_substitution
        )
                    ->make();

    }
}
