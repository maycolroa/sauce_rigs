<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsDangerDescription;
use Session;

class DangerDescriptionController extends Controller
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
        $danger_descriptions = TagsDangerDescription::select('*');

        return Vuetable::of($danger_descriptions)
                    ->make();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsDangerDescription  $danger_descriptions
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsDangerDescription $dangerDescription)
    {
        if(!$dangerDescription->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }
}
