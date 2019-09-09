<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsPossibleConsequencesDanger;
use Session;

class PossibleConsequencesDangerController extends Controller
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
        $consequences = TagsPossibleConsequencesDanger::select('*');

        return Vuetable::of($consequences)
                    ->make();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsPossibleConsequencesDanger  $consequences
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsPossibleConsequencesDanger $possibleConsequencesDanger)
    {
        if(!$possibleConsequencesDanger->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }
}
