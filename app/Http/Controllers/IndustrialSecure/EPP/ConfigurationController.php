<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\Location;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\Administrative\Users\User;

class ConfigurationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:configurations_epp_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:configurations_epp_c, {$this->team}");*/
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
            if ($value)
            {
                if ($key == 'users_notify_element_expired' || $key == 'users_notify_stock_minimun')
                {
                    $values = $this->getDataFromMultiselect($value);

                    $users = [];
                    
                    foreach ($values as $id) 
                    {
                        $user = User::find($id);

                        array_push($users, $user->email);
                    }

                    $value = implode(',', $users);
                }

                if ($key == 'multiselect_user_id' || $key == 'multiselect_user_stock_id')
                    continue;

                ConfigurationsCompany::key($key)->value($value)->save();

                if ($key == 'inventory_management')
                {
                    if ($value == 'NO')
                    {
                        $elements = Element::get();

                        foreach ($elements as $key => $value) {
                           $value->identify_each_element = false;
                           $value->save();

                           $this->storeBalanceLocation($value->id);
                        }
                    }
                }
            }
        }

        $this->saveLogActivitySystem('Epp - Configuración', 'Se creo o modifico la configuracion');

        return $this->respondHttp200([
            'message' => 'Se actualizó la configuración'
        ]);
    }

    public function storeBalanceLocation($element_id)
    {
        $locations = Location::get();

        foreach ($locations as $key => $value) 
        {
            $exits = ElementBalanceLocation::where('element_id', $element_id)->where('location_id', $value->id)->first();

            if (!$exits)
            {
                $element = new ElementBalanceLocation();
                $element->element_id = $element_id;
                $element->location_id = $value->id;
                $element->quantity = 0;
                $element->quantity_available = 0;
                $element->quantity_allocated = 0;
                $element->save();
            }
        }
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
            $data = ConfigurationsCompany::findall();

            foreach ($data as $key => $value) 
            {
                if ($key == 'users_notify_element_expired')
                {
                    if ($value)
                    {
                        $users = explode(',', $value);

                        $multiselect = [];

                        foreach ($users as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            array_push($multiselect, $user->multiselect());
                        }
                    }
                }
                
                if ($key == 'users_notify_stock_minimun')
                {
                    if ($value)
                    {
                        $users_stock = explode(',', $value);

                        $multiselect_stock = [];

                        foreach ($users_stock as $email) 
                        {
                            $user = User::where('email', $email)->first();

                            array_push($multiselect_stock, $user->multiselect());
                        }
                    }
                }

                if ($key == 'multiselect_user_id')
                    continue;
            }

            if (isset($multiselect) && count($multiselect) > 0)
            {
                $data['users_notify_element_expired'] = $multiselect;
                $data['multiselect_user_id'] = $multiselect;
            }

            if (isset($multiselect_stock) && count($multiselect_stock) > 0)
            {
                $data['users_notify_stock_minimun'] = $multiselect_stock;
                $data['multiselect_user_stock_id'] = $multiselect_stock;
            }

            return $this->respondHttp200([
                'data' => $data
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
