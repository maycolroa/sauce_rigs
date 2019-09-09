<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsParticipant;
use Session;

class ParticipantsController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dangerMatrix_c');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsParticipant  $participant
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsParticipant $participant)
    {

        if(!$participant->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }
}
