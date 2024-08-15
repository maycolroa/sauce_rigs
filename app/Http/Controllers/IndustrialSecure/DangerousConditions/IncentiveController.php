<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
//use App\Http\Requests\Administrative\Configuration\ConfigurationRequest;
use App\Models\General\Company;
use Carbon\Carbon;
use Validator;
use DB;

class IncentiveController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:ph_inspections_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:ph_inspections_r, {$this->team}");
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
            "incentive" => [
                function ($attribute, $value, $fail)
                {
                     if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'pdf')
                            $fail('Archivo debe ser un pdf');
                    }
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $company = Company::find($this->company);

            if ($request->ph_state_incentives == true)
                $company->ph_state_incentives = 1;
            else
                $company->ph_state_incentives = 0;

            if ($request->ph_file_incentives != $company->ph_file_incentives)
            {
                if ($request->ph_file_incentives)
                {
                    $file = $request->ph_file_incentives;
                    Storage::disk('local')->delete('file_incentives/'. $company->ph_file_incentives);
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->getClientOriginalExtension();
                    $file->storeAs('file_incentives/', $nameFile, 'local');
                    $company->ph_file_incentives = $nameFile;
                }
            }

            if (!$company->update())
                return $this->respondHttp500();

            DB::commit();

            return $this->respondHttp200([
                'data' => $company
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se guardo el incentivo'
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
            $company = Company::select('ph_file_incentives', 'ph_state_incentives')->find($this->company);
            $company->old_ph_file_incentives = $company->ph_file_incentives;
            $company->ph_file_incentives_path = Storage::disk('local')->url('file_incentives/'. $company->ph_file_incentives);

            return $this->respondHttp200([
                'data' => $company
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function download()
    {
        $company = Company::find($this->company);
        return Storage::disk('local')->download('file_incentives/'. $company->ph_file_incentives);
    }
}
