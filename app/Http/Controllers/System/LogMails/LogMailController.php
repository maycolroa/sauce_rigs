<?php

namespace App\Http\Controllers\System\LogMails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\LogMails\LogMail;
use Carbon\Carbon;
use DB;

class LogMailController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:logMails_r, {$this->team}");
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
        $mails = LogMail::selectRaw(
                    'sau_log_mails.*,
                     IF(LENGTH(sau_log_mails.recipients) > 50, CONCAT(SUBSTRING(sau_log_mails.recipients, 1, 50), "..."), sau_log_mails.recipients) AS recipients,
                     sau_modules.display_name AS module,
                     sau_companies.name AS company'
                )
                ->join('sau_modules', 'sau_modules.id', 'sau_log_mails.module_id')
                ->leftJoin('sau_companies', 'sau_companies.id', 'sau_log_mails.company_id');

        $filters = $request->get('filters');

        if (COUNT($filters) > 0)
        {
            $mails->inCompanies($this->getValuesForMultiselect($filters["companies"]), $filters['filtersType']['companies']);
            $mails->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);
        }

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
            $mail->company;

            return $this->respondHttp200([
                'data' => $mail,
            ]);
        } catch(Exception $e){
            return $this->respondHttp500();
        }
    }
}
