<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\General\Team;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\SendNotification;
use App\Http\Requests\LegalAspects\Contracts\SendNotificationRequest;
use App\Facades\Mail\Facades\NotificationMail;
use DB;
use Validator;
use App\Models\System\LogMails\LogMail;


class SendNotificationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:newsletterSend_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:newsletterSend_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:newsletterSend_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:newsletterSend_d, {$this->team}", ['only' => 'destroy']);*/
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
        $notifications = SendNotification::select(
            '*',
            DB::raw("case when sau_ct_send_notifications.active is true then 'SI' else 'NO' end as active_send"),
            DB::raw("case when sau_ct_send_notifications.send is true then 'SI' else 'NO' end as send_2")
        );

        return Vuetable::of($notifications)
            ->addColumn('switchStatus', function ($notification) {
                return true; 
            })
            ->addColumn('contract-send-notification-edit', function ($notification) {
                if ($notification->active || $notification->send)
                    return false; 
                else
                    return true;
            })
            /*->addColumn('system-newslettersend-program', function ($notification) {
                if ($notification->active)
                    return true; 
                else
                    return false;
            })*/
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\SendNotificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SendNotificationRequest $request)
    {
        DB::beginTransaction();
        
        try
        {
            $notificationSend = new SendNotification;
            $notificationSend->subject = $request->subject;
            $notificationSend->body = $request->body;
            $notificationSend->company_id = $this->company;
        
            if (!$notificationSend->save())
                return $this->respondHttp500();


            if ($request->has('activity_id') && is_array($request->activity_id))
            {
                foreach ($request->activity_id as $key => $value)
                {
                    $data['activity_id'][$key] = json_decode($value, true);
                    $request->merge($data);
                }

                $notificationSend->activities()->sync($this->getValuesForMultiselect($request->activity_id));
            }
            
            if ($request->has('contract_id') && is_array($request->contract_id))
            {
                foreach ($request->contract_id as $key => $value)
                {
                    $data['contract_id'][$key] = json_decode($value, true);
                    $request->merge($data);
                }

                $notificationSend->contracts()->sync($this->getValuesForMultiselect($request->contract_id));
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el boletin'
        ]);
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
            $notificationSend = SendNotification::findOrFail($id);

            $activities = [];
            $contracts = [];

            if ($notificationSend->activities->count() > 0)
            {
                $notificationSend->type = 'Actividad';

                foreach ($notificationSend->activities as $value)
                {
                    array_push($activities, $value->multiselect());
                }

                $notificationSend->multiselect_activity = $activities;
                $notificationSend->activity_id = $activities;
            }

            if ($notificationSend->contracts->count() > 0)
            {
                $notificationSend->type = 'Contratista';

                foreach ($notificationSend->contracts as $value)
                {
                    array_push($contracts, $value->multiselect());
                }

                $notificationSend->multiselect_contracts = $contracts;
                $notificationSend->contract_id = $contracts;
            }

            return $this->respondHttp200([
                'data' => $notificationSend,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\SendNotificationRequest  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(SendNotificationRequest $request, SendNotification $notificationSend)
    {
        DB::beginTransaction();

        try
        {
            $notificationSend->subject = $request->subject;
            $notificationSend->body = $request->body;
           
            if(!$notificationSend->update())
                return $this->respondHttp500();

            if ($request->has('activity_id') && is_array($request->activity_id))
            {
                foreach ($request->activity_id as $key => $value)
                {
                    $data['activity_id'][$key] = json_decode($value, true);
                    $request->merge($data);
                }

                $notificationSend->activities()->sync($this->getValuesForMultiselect($request->activity_id));
            }
            
            if ($request->has('contract_id') && is_array($request->contract_id))
            {
                foreach ($request->contract_id as $key => $value)
                {
                    $data['contract_id'][$key] = json_decode($value, true);
                    $request->merge($data);
                }

                $notificationSend->contracts()->sync($this->getValuesForMultiselect($request->contract_id));
            }
                
            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el boletin'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notificationSend = SendNotification::findOrFail($id);
        
        if(!$notificationSend->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el boletin'
        ]);
    }

    /**
     * toggles the check state between open and close
     * @param  Company $company
     * @return \Illuminate\Http\Response
     */

    public function toggleState(SendNotification $notificationSend)
    {
        $data = ['active' => !$notificationSend->active];

        if (!$notificationSend->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del boletin'
        ]);
    }

    public function download($id)
    {
        $newsletter = SendNotification::find($id);
        return Storage::disk('s3')->download('newsletters/'. $newsletter->image, $newsletter->image_name);
    }

    public function programSend(Request $request, $notificationSend)
    {
        $notificationSend = SendNotification::findOrFail($notificationSend);
        
        $data = [
            'date_send' => $this->formatDateToSave($request->get('date_send')),
            'hour' => $request->hour
        ];

        if (!$notificationSend->update($data)) {
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se programo el envio'
        ]);
    }


    public function reportOpensEmails(Request $request)
    {
        $notificationSend = SendNotification::find($request->get('modelId'));

        $emails = LogMail::select(
            "sau_log_mails.id AS id",
            "sau_log_mails.company_id AS company_id",
            "sau_log_mails.recipients AS email",
            "sau_users.name AS user",
            DB::raw("case when sent_emails.opens > 0 then 'SI' else 'NO' end AS open")
        )
        ->withoutGlobalScopes()
        ->join('sent_emails', 'sent_emails.message_id', 'sau_log_mails.message_id')
        ->join('sau_companies', 'sau_companies.id', 'sau_log_mails.company_id')
        ->join('sau_users', 'sau_users.email', 'sau_log_mails.recipients')
        ->where('sau_log_mails.module_id', 1)
        ->where('sau_log_mails.event', DB::raw("'Tarea programada: SendNewsletterEmail'"))
        ->where('sau_log_mails.subject', $notificationSend->subject);
        
        return Vuetable::of($emails)->make();
    }

    public function sendMailManual(SendNotification $newsletter)
    {
        $newsletter->path = Storage::disk('s3')->url('newsletters/'. $newsletter->image);

        NotificationMail::
            subject($newsletter->subject)
            ->view('system.newsletters.newsletter')
            ->recipients($this->user)
            ->module('users')
            ->event('Prueba: SendNewsletterEmail')
            ->with(['data' => $newsletter])
            ->company(1)
            ->send();

    }

    public function getListContract(Request $request)
    {
        $information = DB::table('sau_user_information_contract_lessee')
        ->selectRaw('MIN(user_id) AS user_id, information_id')
        ->groupBy('information_id');

        $contracts = ActivityContract::select(
            'sau_ct_information_contract_lessee.id',
            'sau_ct_information_contract_lessee.nit', 
            'sau_ct_information_contract_lessee.social_reason', 
            'sau_ct_information_contract_lessee.classification', 
            'sau_users.name', 
            'sau_users.email'
        )
        ->join('sau_ct_contracts_activities', 'sau_ct_contracts_activities.activity_id', 'sau_ct_activities.id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_contracts_activities.contract_id')
        ->join(DB::raw("({$information->toSql()}) as t"), function ($join) 
            {
                $join->on("t.information_id", "sau_ct_information_contract_lessee.id");
            }
        )
        ->join('sau_users', 'sau_users.id', 't.user_id')
        ->whereIn('sau_ct_activities.id', $this->getValuesForMultiselect($request->activities))
        ->get();

        return $this->respondHttp200([
            'data' => $contracts
        ]);
    }
}
