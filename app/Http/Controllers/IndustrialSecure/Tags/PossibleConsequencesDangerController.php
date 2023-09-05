<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class PossibleConsequencesDangerController extends Controller
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
        $consequences = TagsPossibleConsequencesDanger::select('*');

        return Vuetable::of($consequences)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsPossibleConsequencesDanger($request->all());
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Posibles consecuencias del peligro', 'Se creo el tag '.$tag->name.' ');

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
        \Log::info($id);
        try
        {
            $tag = TagsPossibleConsequencesDanger::findOrFail($id);
            $tag->rewrite = '';
            $tag->replace = '';
            $tag->replace_deleted = '';

            return $this->respondHttp200([
                'data' => $tag,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
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
    public function update(Request $request, TagsPossibleConsequencesDanger $possibleConsequencesDanger)
    {
        if ($request->rewrite)
        {
            $name_old = $possibleConsequencesDanger->name;
            $possibleConsequencesDanger->fill($request->all());
            
            if(!$possibleConsequencesDanger->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $possibleConsequencesDanger->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Posibles consecuencias del peligro', 'Se edito el tag '.$possibleConsequencesDanger->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsPossibleConsequencesDanger::find($request->replace_deleted);

            $this->rewriteTag($possibleConsequencesDanger->name, $new_tag->name);
            $this->destroy($possibleConsequencesDanger);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($possibleConsequencesDanger->name, '');
            $this->destroy($possibleConsequencesDanger);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $possible_consequences_danger_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.possible_consequences_danger
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.possible_consequences_danger', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($possible_consequences_danger_data as $key => $value) 
        {
            if ($value->possible_consequences_danger)
            {
                $controls = explode(',', $value->possible_consequences_danger);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->implode(",");

                $value->possible_consequences_danger = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsPossibleConsequencesDanger  $possibleConsequencesDanger
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsPossibleConsequencesDanger $possibleConsequencesDanger)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Posibles consecuencias del peligro', 'Se elimino el tag '.$possibleConsequencesDanger->name.' ');

        if(!$possibleConsequencesDanger->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $possible_consequences_danger = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - Posibles Consecuencias del Peligro' campo,  
            sau_dm_activity_danger.possible_consequences_danger as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->where('sau_dm_activity_danger.possible_consequences_danger', 'like', "%$request->keyword%");

        return Vuetable::of($possible_consequences_danger
        )
                    ->make();

    }
}
