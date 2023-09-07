<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;

class ParticipantsController extends Controller
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
        $participants = TagsParticipant::select('*');

        return Vuetable::of($participants)
                    ->make();
    }

    public function store(Request $request)
    {
        $tag = new TagsParticipant($request->all());
        $tag->company_id = $this->company;
        
        if(!$tag->save()){
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Matriz de peligros - TAG Participantes', 'Se creo el tag '.$tag->name.' ');

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
            $tag = TagsParticipant::findOrFail($id);
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
    public function update(Request $request, TagsParticipant $participant)
    {
        if ($request->rewrite)
        {
            $name_old = $participant->name;
            $participant->fill($request->all());
            
            if(!$participant->update()){
            return $this->respondHttp500();
            }

            if ($request->rewrite == 'SI')
                $this->rewriteTag($name_old, $participant->name);

            $this->saveLogActivitySystem('Matriz de peligros - TAG Participantes', 'Se edito el tag '.$participant->name.' ');
            
            return $this->respondHttp200([
                'message' => 'Se actualizo el tag'
            ]);
        }
        else if ($request->replace && $request->replace == 'SI')
        { 
            $new_tag = TagsParticipant::find($request->replace_deleted);

            $this->rewriteTag($participant->name, $new_tag->name);
            $this->destroy($participant);
        }
        else if ($request->replace && $request->replace == 'NO')
        {
            $this->rewriteTag($participant->name, '');
            $this->destroy($participant);
        }
    }

    public function rewriteTag($old_name, $new_name)
    {
        $participants_data = DangerMatrix::selectRaw("
            sau_dangers_matrix.id,
            sau_dangers_matrix.participants
        ")
        ->where('sau_dangers_matrix.participants', 'like', "%$old_name%")
        ->get();

        foreach ($participants_data as $key => $value) 
        {
            if ($value->participants)
            {
                $controls = explode(',', $value->participants);
                $controls = collect($controls)->map(function ($item, $key) use ($old_name, $new_name) {
                    return $item == $old_name ? $new_name : $item;
                })
                ->filter(function ($item, $key) {
                    return $item;
                })
                ->implode(",");

                $value->participants = $controls;
                $value->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsParticipant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsParticipant $participant)
    {
        $this->saveLogActivitySystem('Matriz de peligros - TAG Participantes', 'Se elimino el tag '.$participant->name.' ');

        if(!$participant->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }

    public function sharedTag(Request $request)
    {
        $participants = DangerMatrix::selectRaw("
            sau_dangers_matrix.name as matriz,
            sau_dm_activities.name as activity, 
            sau_dm_dangers.name as danger, 
            'Participantes' campo,  
            sau_dangers_matrix.participants as value
        ")
        ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
        ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
        ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
        ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
        ->whereRaw("FIND_IN_SET('$request->keyword', sau_dangers_matrix.participants) > 0");

        return Vuetable::of($participants
        )
                    ->make();

    }
}
