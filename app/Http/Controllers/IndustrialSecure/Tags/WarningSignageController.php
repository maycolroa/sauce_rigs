<?php

namespace App\Http\Controllers\IndustrialSecure\Tags;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\TagsWarningSignage;
use Session;

class WarningSignageController extends Controller
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
        $warning_signage = TagsWarningSignage::select('*');

        return Vuetable::of($warning_signage)
                    ->make();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TagsWarningSignage  $warning_signage
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagsWarningSignage $warningSignage)
    {

        if(!$warningSignage->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tag'
        ]);
    }
}
