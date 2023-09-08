<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\ActivityDanger;

class DangerDescriptionController extends Controller
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
        $danger_descriptions = TagsDangerDescription::select('*');

        return Vuetable::of($danger_descriptions)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsDangerDescription($request->all());
        $tag->name = trim(str_replace(',', '', $request->name));
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
        try
        {
            $tag = TagsDangerDescription::findOrFail($id);
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
    public function update(Request $request, TagsDangerDescription $dangerDescription)
    {
        if ($request->rewrite)
        {
            $name_old = $dangerDescription->name;
            $dangerDescription->fill($request->all());
            $dangerDescription->name = trim(str_replace(',', '', $request->name));
            
            if(!$dangerDescription->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $dangerDescription->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Posibles consecuencias del peligro', 'Se edito el tag '.$dangerDescription->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsDangerDescription::find($request->replace_deleted);

            $this->rewriteTag($dangerDescription->name, $new_tag->name);
            $this->destroy($dangerDescription);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($dangerDescription->name, '');
            $this->destroy($dangerDescription);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $danger_description_data = ActivityDanger::selectRaw("
            sau_dm_activity_danger.id,
            sau_dm_activity_danger.danger_description
        ")
        ->join('sau_danger_matrix_activity', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dangers_matrix', 'sau_dangers_matrix.id', 'sau_danger_matrix_activity.danger_matrix_id')
        ->where('sau_dm_activity_danger.danger_description', 'like', "%$old_name%")
        ->where('company_id', $this->company)
        ->get();

        foreach ($danger_description_data as $key => $value) 
        {
            if ($value->danger_description)
            {
                $controls = explode(',', $value->danger_description);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->danger_description = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsDangerDescription  $TagsDangerDescription
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsDangerDescription $dangerDescription)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Posibles consecuencias del peligro', 'Se elimino el tag '.$dangerDescription->name.' ');

        if(!$dangerDescription->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $danger_description = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Controles Existentes - Descripcion del peligro' campo,  
            sau_dm_activity_danger.danger_description as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dm_activity_danger.danger_description) > 0");

        return Vuetable::of($danger_description
        )
                    ->make();

    }
}
