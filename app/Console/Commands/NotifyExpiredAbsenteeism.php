<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\LogNotifyExpired;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use Carbon\Carbon;
use DB;

class NotifyExpiredAbsenteeism extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-expired-absenteeism';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de notificacion por vencimiento cercano de incapacidades de ausentismo, los dias anticipados seran configurados por el cliente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            DB::beginTransaction();

            $companies = License::selectRaw('DISTINCT company_id')
                ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
                ->withoutGlobalScopes()
                ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
                ->where('sau_license_module.module_id', '24');

            $companies = $companies->pluck('sau_licenses.company_id');

            foreach ($companies as $key => $company)
            {
                $expired = [];

                $company_get = Company::find($company);

                if ($company_get->id == 1 || $company_get->id == 130)
                    continue;

                $configDay = $this->getConfig($company);

                if (!$configDay)
                    continue;

                $responsibles_bbdd = ConfigurationsCompany::company($company)->findByKey('users_notify_expired_absenteeism_expired');

                if ($company_get->id == 605 || $company_get->id == 499)
                {
                    \Log::info('Comenzo tarea ausentismo: '.Carbon::now());
                    
                    try
                    {
                        $records = DB::connection('ausentismo')
                        ->table("ausentismo.Ausentismo_$company_get->id")
                        //->limit(100)
                        ->get();

                    } catch (\Exception $e) {
                        continue;
                    }

                    $expired_email_1_alert = [];
                    $expired_email_2_alert = [];                
                    $responsibles = [];
                    $number_notification = 0;

                    foreach ($records as $key => $record) 
                    {
                        if ($company_get->id == 605)
                        {
                            if ($record->IndicadorProrroga == 'NO')
                            {
                                $prorrogas = collect($records->where('prorroga', $record->id)
                                ->where('IndicadorProrroga', 'SI')->all());

                                if ($prorrogas->count() > 0)
                                {
                                    $days = $prorrogas->sum('Dias') + $record->Dias;
                                    $fecha_max = $prorrogas->max('FechaFinal');

                                    if ($fecha_max > Carbon::now()->format('Y-m-d'))
                                    {
                                        if ($responsibles_bbdd)
                                            $responsibles = explode(',', $responsibles_bbdd);

                                        $fecha_max = Carbon::parse($fecha_max)->format('Y-m-d');
                                        $fecha_ini = Carbon::parse($record->FechaInicial)->format('Y-m-d');

                                        $content = [
                                            'Identificacion' => $record->Identificacion,
                                            'Nombre' => $record->Nombre,
                                            'Codigo Diagnostico' => $record->CodigoDiagnostico,
                                            'Nombre Diagnostico' => $record->NombreDiagnostico,
                                            'Tipo Ausentismo' => $record->TipoAusentismo,
                                            'Fecha Inicial' => $fecha_ini,
                                            'Dias' => $days
                                        ];

                                        if (COUNT($configDay) == 2)
                                        {
                                            if ($days >= $configDay[1])
                                                $number_notification = 1;
                                        }
                                        else
                                        {                                        
                                            if ($days >= $configDay[1] && $days < $configDay[2])
                                            {
                                                $notify_1_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 1);


                                                if ($notify_1_exist)
                                                {
                                                    array_push($expired_email_1_alert, $content);

                                                    $insert_email_record = new LogNotifyExpired;
                                                    $insert_email_record->company_id = $company;
                                                    $insert_email_record->document = $record->Identificacion;
                                                    $insert_email_record->id_consecutivo = $record->id;
                                                    $insert_email_record->number_notification = 1;
                                                    $insert_email_record->email_send = $responsibles_bbdd;
                                                    $insert_email_record->save();
                                                }                                            
                                            }
                                            else if ($days >= $configDay[2])
                                            {
                                                $notify_2_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 2);

                                                if ($notify_2_exist)
                                                {
                                                    array_push($expired_email_2_alert, $content);

                                                    $insert_email_record = new LogNotifyExpired;
                                                    $insert_email_record->company_id = $company;
                                                    $insert_email_record->document = $record->Identificacion;
                                                    $insert_email_record->id_consecutivo = $record->id;
                                                    $insert_email_record->number_notification = 2;
                                                    $insert_email_record->email_send = $responsibles_bbdd;
                                                    $insert_email_record->save();
                                                }

                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $fecha_max = Carbon::parse($record->FechaFinal)->format('Y-m-d');
                                    $fecha_ini = Carbon::parse($record->FechaInicial)->format('Y-m-d');
                                    $days = $record->Dias;

                                    if ($fecha_max > Carbon::now()->format('Y-m-d'))
                                    {
                                        if ($responsibles_bbdd)
                                            $responsibles = explode(',', $responsibles_bbdd);

                                        $content = [
                                            'Identificacion' => $record->Identificacion,
                                            'Nombre' => $record->Nombre,
                                            'Codigo Diagnostico' => $record->CodigoDiagnostico,
                                            'Nombre Diagnostico' => $record->NombreDiagnostico,
                                            'Tipo Ausentismo' => $record->TipoAusentismo,
                                            'Fecha Inicial' => $fecha_ini,
                                            'Dias' => $days
                                        ];

                                        if (COUNT($configDay) == 2)
                                        {
                                            if ($days >= $configDay[1])
                                                $number_notification = 1;
                                        }
                                        else
                                        {
                                            if ($days >= $configDay[1] && $days < $configDay[2])
                                            {
                                                $notify_1_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 1);

                                                if ($notify_1_exist)
                                                {
                                                    array_push($expired_email_1_alert, $content);

                                                    $insert_email_record = new LogNotifyExpired;
                                                    $insert_email_record->company_id = $company;
                                                    $insert_email_record->document = $record->Identificacion;
                                                    $insert_email_record->id_consecutivo = $record->id;
                                                    $insert_email_record->number_notification = 1;
                                                    $insert_email_record->email_send = $responsibles_bbdd;
                                                    $insert_email_record->save();
                                                }                                            
                                            }
                                            else if ($days >= $configDay[2])
                                            {
                                                $notify_2_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 2);

                                                if ($notify_2_exist)
                                                {
                                                    array_push($expired_email_2_alert, $content);

                                                    $insert_email_record = new LogNotifyExpired;
                                                    $insert_email_record->company_id = $company;
                                                    $insert_email_record->document = $record->Identificacion;
                                                    $insert_email_record->id_consecutivo = $record->id;
                                                    $insert_email_record->number_notification = 2;
                                                    $insert_email_record->email_send = $responsibles_bbdd;
                                                    $insert_email_record->save();
                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else if ($company_get->id == 499)
                        {
                            $fecha_max = Carbon::parse($record->FechaFinal)->format('Y-m-d');
                            $fecha_ini = Carbon::parse($record->FechaInicial)->format('Y-m-d');
                            $days = $record->Dias;

                            if ($fecha_max > Carbon::now()->format('Y-m-d'))
                            {
                                if ($responsibles_bbdd)
                                    $responsibles = explode(',', $responsibles_bbdd);

                                $content = [
                                    'Identificacion' => $record->Identificacion,
                                    'Nombre' => $record->Nombre,
                                    'Codigo Diagnostico' => $record->CodigoDiagnostico,
                                    'Nombre Diagnostico' => $record->NombreDiagnostico,
                                    'Tipo Ausentismo' => $record->TipoAusentismo,
                                    'Fecha Inicial' => $fecha_ini,
                                    'Dias' => $days
                                ];

                                if (COUNT($configDay) == 2)
                                {
                                    if ($days >= $configDay[1])
                                        $number_notification = 1;
                                }
                                else
                                {
                                    if ($days >= $configDay[1] && $days < $configDay[2])
                                    {
                                        $notify_1_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 1);

                                        if ($notify_1_exist)
                                        {
                                            array_push($expired_email_1_alert, $content);

                                            $insert_email_record = new LogNotifyExpired;
                                            $insert_email_record->company_id = $company;
                                            $insert_email_record->document = $record->Identificacion;
                                            $insert_email_record->id_consecutivo = $record->id;
                                            $insert_email_record->number_notification = 1;
                                            $insert_email_record->email_send = $responsibles_bbdd;
                                            $insert_email_record->save();
                                        }                                            
                                    }
                                    else if ($days >= $configDay[2])
                                    {
                                        $notify_2_exist = $this->checkSend($record->Identificacion, $record->id, $company_get->id, 2);

                                        if ($notify_2_exist)
                                        {
                                            array_push($expired_email_2_alert, $content);

                                            $insert_email_record = new LogNotifyExpired;
                                            $insert_email_record->company_id = $company;
                                            $insert_email_record->document = $record->Identificacion;
                                            $insert_email_record->id_consecutivo = $record->id;
                                            $insert_email_record->number_notification = 2;
                                            $insert_email_record->email_send = $responsibles_bbdd;
                                            $insert_email_record->save();
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    continue;
                }                

                if (COUNT($expired_email_1_alert) > 0)
                {            
                    if (count($responsibles) > 0)
                    {
                        foreach ($responsibles as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Primera Alerta Incapacidades')
                                ->recipients($recipient)
                                ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>$configDay[1]</b> dias.")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->view('preventiveoccupationalmedicine.abssenteeism.notifyExpiredAbssen')
                                ->with(['data'=>$expired_email_1_alert])
                                //->table($expired_email_1_alert)
                                ->company($company)
                                ->send();
                        }
                    } 
                }

                if (COUNT($expired_email_2_alert) > 0)
                {            
                    if (count($responsibles) > 0)
                    {
                        foreach ($responsibles as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Segunda Alerta Incapacidades')
                                ->recipients($recipient)
                                ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>$configDay[2]</b> dias.")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->view('preventiveoccupationalmedicine.abssenteeism.notifyExpiredAbssen')
                                ->with(['data'=>$expired_email_2_alert])
                                //->table($expired_email_2_alert)
                                ->company($company)
                                ->send();
                        }
                    } 
                }
            }

            \Log::info('Termino tarea ausentismo: '.Carbon::now());

            DB::commit();

        } catch(Exception $e){
            DB::rollback();
            $this->respondHttp500();
        }
    }

    public function getConfig($company_id)
    {
        $key = "expired_absenteeism";
        $key1 = "days_alert_expiration_date_absenteeism";
        $key2 = "days_alert_expiration_date_absenteeism_2";
        
        try
        {
            $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

            if ($exists && $exists == 'SI')
            {
                $days = [];
                array_push($days, 0);
                $days_1 = ConfigurationsCompany::company($company_id)->findByKey($key1);
                array_push($days, $days_1);

                $days_2 = ConfigurationsCompany::company($company_id)->findByKey($key2);

                if ($days_2 && $days_2 > 0)
                    array_push($days, $days_2);
    
                return $days;
            }
            else
                return NULL;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    public function checkSend($ident, $id_consecutivo, $company_id, $number)
    {
        $var = LogNotifyExpired::where('company_id', $company_id)
        ->where('document', $ident)->where('id_consecutivo', $id_consecutivo)->where('number_notification', $number)->first();

        if ($var)
            return false;
        else
            return true;
    }
}
