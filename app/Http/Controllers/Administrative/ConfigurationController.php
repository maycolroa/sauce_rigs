<?php

namespace App\Http\Controllers\Administrative;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use Session;

class ConfigurationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:configurations_c|configurations_u', ['only' => 'store']);
        $this->middleware('permission:configurations_r');
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configuration\ConfigurationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConfigurationRequest $request)
    {
        $request = $request->except('_method');

        foreach ($request as $key => $value)
        {
            ConfigurationsCompany::key($key)->value($value)->save();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizó la configuración'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try
        {
            return $this->respondHttp200([
                'data' => ConfigurationsCompany::findall()
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Returns an arrangement with the Location Levels
     *
     * @return Array
     */
    public function radioLocationLevels()
    {
      $options = ["Regional"=>"Regional", "Sede"=>"Sede", "Área"=>"Área", "Proceso"=>"Proceso"];
      return $this->radioFormat(collect($options));
    }
}
