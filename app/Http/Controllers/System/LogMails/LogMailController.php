<?php

namespace App\Http\Controllers\System\LogMails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\LogMails\LogMail;
use App\Traits\Filtertrait;
use Carbon\Carbon;
use DB;

class LogMailController extends Controller
{
    use Filtertrait;

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
                    'sau_log_mails.id,
                     sau_log_mails.event,
                     sau_log_mails.subject,
                     sau_log_mails.created_at,
                     IF(LENGTH(sau_log_mails.recipients) > 50, CONCAT(SUBSTRING(sau_log_mails.recipients, 1, 50), "..."), sau_log_mails.recipients) AS recipients,
                     sau_modules.display_name AS module,
                     sau_companies.name AS company,
                     "SI" AS view'
                )
                ->join('sau_modules', 'sau_modules.id', 'sau_log_mails.module_id')
                ->leftJoin('sau_companies', 'sau_companies.id', 'sau_log_mails.company_id')
                ->orderBy('sau_log_mails.created_at', 'DESC');

        $mails2 = DB::table('sent_emails')->selectRaw("
                    sent_emails.id,
                    'Restablecer contraseña' AS event,
                    sent_emails.subject,
                    sent_emails.created_at,
                    IF(LENGTH(sent_emails.recipient) > 50, CONCAT(SUBSTRING(sent_emails.recipient, 1, 50), '...'), sent_emails.recipient) AS recipients,
                    'Usuarios' AS module,
                    'RIGS' AS company,
                    'NO' AS view
                ")
                ->where('sent_emails.subject', DB::raw("'SAUCE - Restablecer contraseña'"))
                ->orderBy('sent_emails.created_at', 'DESC');


        $url = "/system/logmails";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters['companies']) > 0)
            $mails->inCompanies($this->getValuesForMultiselect($filters["companies"]), $filters['filtersType']['companies']);
        if (COUNT($filters['modules']) > 0)
            $mails->inModules($this->getValuesForMultiselect($filters["modules"]), $filters['filtersType']['modules']);
        
        if (COUNT($filters['companies']) == 0 && COUNT($filters['modules']) == 0)
        {
            $mails = $mails->union($mails2)->orderBy('created_at', 'DESC');
        }

        return Vuetable::of($mails)
            ->addColumn('system-logmails-view', function ($mail) {
                if ($mail->view == 'SI')
                    return true;
                else 
                    return false;
            })
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
