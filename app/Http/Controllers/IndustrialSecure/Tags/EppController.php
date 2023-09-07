<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsEpp;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class EppController extends Controller
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
        $epp = TagsEpp::select('*');

        return Vuetable::of($epp)
                    ->make();
    }
    public function store(Request $request)
    {
        $tag = new TagsEpp($request->all());
        $tag->name = trim(str_replace(',', '', $request->name));
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles EPP', 'Se creo el tag '.$tag->name.' ');

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
            $tag = TagsEpp::findOrFail($id);
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
    public function update(Request $request, TagsEpp $epp)
    {
        if ($request->rewrite)
        {
            $name_old = $epp->name;
            $epp->fill($request->all());
            
            if(!$epp->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $epp->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Controles administrativos', 'Se edito el tag '.$epp->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        {
            $new_tag = TagsEpp::find($request->replace_deleted);

            $this->rewriteTag($epp->name, trim($new_tag->name));
            $this->destroy($epp);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($epp->name, '');
            $this->destroy($epp);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $existing_controls_epp_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.existing_controls_epp
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.existing_controls_epp', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($existing_controls_epp_data as $key => $value) 
        {
           if ($value->existing_controls_epp)
            {
                $controls = explode(',', $value->existing_controls_epp);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->existing_controls_epp = $controls;
                $value->save();
            }
        }

        $intervention_measures_epp_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.intervention_measures_epp
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.intervention_measures_epp', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($intervention_measures_epp_data as $key => $value) 
        {
           if ($value->intervention_measures_epp)
            {
                $controls = explode(',', $value->intervention_measures_epp);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->intervention_measures_epp = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsEpp  $epp
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsEpp $epp)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Controles Epp', 'Se elimino el tag '.$epp->name.' ');
        if(!$epp->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $existing_controls_epp = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - EPP' campo,  
            sau_dm_activity_danger.existing_controls_epp as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.existing_controls_epp) > 0");


        $intervention_measures_epp = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Medidas de prevencion - EPP' campo,  
            sau_dm_activity_danger.intervention_measures_epp as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.intervention_measures_epp) > 0");

        $existing_controls_epp->union($intervention_measures_epp);

        return Vuetable::of($existing_controls_epp)->make();

    }
}
