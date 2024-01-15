<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Helpers\Helper;
use App\Models\System\Helpers\HelperFile;
use App\Models\General\Module;
use App\Http\Requests\System\Helpers\HelperRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Validator;
use DB;


class HelperController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function data(Request $request)
    {

        $helpers = Helper::select(
            'sau_helpers.*'
        )
        ->where('module_id', 31)
        ->orderBy('sau_helpers.id', 'DESC');

        return Vuetable::of($helpers)
                    ->make();
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
            $helper = Helper::findOrFail($id);

            $helper->multiselect_module = $helper->module->multiselect();

            $get_files = HelperFile::where('helper_id', $helper->id)->first();

            $helper->file = [
                'key' => Carbon::now()->timestamp + rand(1,10000),
                'name' => $get_files->name,
                'path' => Storage::disk('s3')->url('system/helpers/files/1/'.$get_files->file),
                'type' => explode('.', $get_files->file)[1]
            ];

            return $this->respondHttp200([
                'data' => $helper,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
