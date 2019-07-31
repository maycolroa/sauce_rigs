<?php

namespace App\Http\Controllers\Administrative\Logos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
//use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Models\General\Company;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;
use Validator;

class LogoController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:logos_c', ['only' => 'store']);
        $this->middleware('permission:logos_r');
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
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            "logo" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && $value->getClientMimeType() != 'image/png')
                        $fail('Imagen debe ser PNG');
                },
            ]
        ])->validate();

        $company = Company::find(Session::get('company_id'));
        $data = $request->all();

        if ($request->logo != $company->logo)
        {
            if ($request->logo)
            {
                $file = $request->logo;
                Storage::disk('public')->delete('administrative/logos/'. $company->logo);
                $nameFile = base64_encode(Auth::user()->id . now() . rand(1,10000)) .'.'. $file->extension();
                $file->storeAs('administrative/logos/', $nameFile, 'public');
                $company->logo = $nameFile;
                $data['logo'] = $nameFile;
                $data['old_logo'] = $nameFile;
                $data['logo_path'] = Storage::disk('public')->url('administrative/logos/'. $nameFile);
            }
            else
            {
                Storage::disk('public')->delete('administrative/logos/'. $company->logo);
                $company->logo = NUlL;
                $data['logo'] = NULL;
                $data['old_logo'] = NULL;
                $data['logo_path'] = NULL;
            }
        }

        if (!$company->update())
            return $this->respondHttp500();

        return $this->respondHttp200([
            'data' => $data
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
            $company = Company::select('logo')->find(Session::get('company_id'));
            $company->old_logo = $company->logo;
            $company->logo_path = Storage::disk('public')->url('administrative/logos/'. $company->logo);

            return $this->respondHttp200([
                'data' => $company
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function download()
    {
        $company = Company::find(Session::get('company_id'));
        return Storage::disk('public')->download('administrative/logos/'. $company->logo);
    }
}
