<?php

namespace App\Http\Controllers\System\LogMails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\LogMails\LogMail;
use Carbon\Carbon;
use Session;
use DB;

class LogMailController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:logMails_r');
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
        $mails = LogMail::select(
                    'sau_log_mails.*',
                    'sau_modules.display_name AS module'
                )
                ->join('sau_modules', 'sau_modules.id', 'sau_log_mails.module_id')
                //->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
                ;

        return Vuetable::of($mails)
                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $mail = LogMail::findOrFail($id);
            $mail->module;

            return $this->respondHttp200([
                'data' => $mail,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
