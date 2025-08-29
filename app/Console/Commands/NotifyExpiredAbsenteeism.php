<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\LogNotifyExpired;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
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
    protected $keys_absenteeism = []; 

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

            \Log::info('Inicio tarea ausentismo: '.Carbon::now());

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

                $this->keys_absenteeism = $this->getConfig($company);

                if (!$this->keys_absenteeism)
                    continue;
                    
                try
                {
                    $table_name = Table::withoutGlobalScopes()->find($this->keys_absenteeism['name_table_absenteeism']);

                    if ($table_name)
                        $name_table = 'sau_absen_'.$company_get->id.'_'.$table_name->name;
                    else
                        continue;

                    $records =DB::table("$name_table as info_employees")
                    ->get();

                } catch (\Exception $e) {
                    \Log::info($e->getMessage());
                    continue;
                }

                $expired_email_1_alert = [];
                $expired_email_2_alert = [];      
                $expired_email_3_alert = [];

                foreach ($records as $key => $record) 
                {
                    $column_fech_ini = $this->keys_absenteeism['name_column_fec_ini_absenteeism'];
                    $column_fech_fin = $this->keys_absenteeism['name_column_fec_fin_absenteeism'];
                    $column_name = $this->keys_absenteeism['name_column_employee_name_absenteeism'];
                    $column_identification = $this->keys_absenteeism['name_column_employee_identification_absenteeism'];
                    $column_cod_diag = $this->keys_absenteeism['name_column_cod_diag_absenteeism'];
                    $column_desc_diag = $this->keys_absenteeism['name_column_employee_description_diag_absenteeism'];

                    $fecha_ini = Carbon::parse($record->$column_fech_ini);
                    $fecha_fin = Carbon::parse($record->$column_fech_fin);
                    $fecha_now = Carbon::now();

                    if ($fecha_fin->gt($fecha_now))
                    {
                        $days = $fecha_ini->diffInDays($fecha_now);

                        if ($this->keys_absenteeism['days_alert_expiration_date_absenteeism_90'] == 'SI')
                        {
                            if ($days >= 90 && $days < 180)
                            {
                                $content = [
                                    'Identificacion' => $record->$column_identification,
                                    'Nombre' => $record->$column_name,
                                    'Codigo Diagnostico' => $record->$column_cod_diag,
                                    'Nombre Diagnostico' => $record->$column_desc_diag,
                                    'Fecha Inicial' => $fecha_ini->format('Y-m-d'),
                                    'Dias' => $days
                                ];

                                $notify_1_exist = $this->checkSend($record->$column_identification, $record->id, $company_get->id, 1);

                                if ($notify_1_exist)
                                {
                                    $insert_email_record = new LogNotifyExpired;
                                    $insert_email_record->company_id = $company_get->id;
                                    $insert_email_record->document = $record->$column_identification;
                                    $insert_email_record->id_consecutivo = $record->id;
                                    $insert_email_record->number_notification = 1;
                                    $insert_email_record->email_send = $this->keys_absenteeism['users_notify_expired_absenteeism_expired_90'];
                                    $insert_email_record->save();
                                    
                                    array_push($expired_email_1_alert, $content);
                                }       

                            }
                        }
                        else if ($this->keys_absenteeism['days_alert_expiration_date_absenteeism_180'] == 'SI')
                        {
                            if ($days >= 180 && $days < 540)
                            {
                                $content = [
                                    'Identificacion' => $record->$column_identification,
                                    'Nombre' => $record->$column_name,
                                    'Codigo Diagnostico' => $record->$column_cod_diag,
                                    'Nombre Diagnostico' => $record->$column_desc_diag,
                                    'Fecha Inicial' => $fecha_ini->format('Y-m-d'),
                                    'Dias' => $days
                                ];

                                $notify_2_exist = $this->checkSend($record->$column_identification, $record->id, $company_get->id, 2);

                                if ($notify_2_exist)
                                {
                                    $insert_email_record = new LogNotifyExpired;
                                    $insert_email_record->company_id = $company_get->id;
                                    $insert_email_record->document = $record->$column_identification;
                                    $insert_email_record->id_consecutivo = $record->id;
                                    $insert_email_record->number_notification = 2;
                                    $insert_email_record->email_send = $this->keys_absenteeism['users_notify_expired_absenteeism_expired_180'];
                                    $insert_email_record->save();
                                    
                                    array_push($expired_email_2_alert, $content);
                                } 
                            }
                        }
                        else if ($this->keys_absenteeism['days_alert_expiration_date_absenteeism_540'] == 'SI')
                        {
                            if ($days >= 540)
                            {
                                $content = [
                                    'Identificacion' => $record->$column_identification,
                                    'Nombre' => $record->$column_name,
                                    'Codigo Diagnostico' => $record->$column_cod_diag,
                                    'Nombre Diagnostico' => $record->$column_desc_diag,
                                    'Fecha Inicial' => $fecha_ini->format('Y-m-d'),
                                    'Dias' => $days
                                ];

                                
                                $notify_3_exist = $this->checkSend($record->$column_identification, $record->id, $company_get->id, 3);

                                if ($notify_3_exist)
                                {
                                    $insert_email_record = new LogNotifyExpired;
                                    $insert_email_record->company_id = $company_get->id;
                                    $insert_email_record->document = $record->$column_identification;
                                    $insert_email_record->id_consecutivo = $record->id;
                                    $insert_email_record->number_notification = 3;
                                    $insert_email_record->email_send = $this->keys_absenteeism['users_notify_expired_absenteeism_expired_540'];
                                    $insert_email_record->save();
                                    
                                    array_push($expired_email_3_alert, $content);
                                } 
                            }
                        }
                    }
                }     

                if (COUNT($expired_email_1_alert) > 0)
                {            
                    $email_alert_1 = explode(',', $this->keys_absenteeism['users_notify_expired_absenteeism_expired_90']);

                    if (count($email_alert_1) > 0)
                    {
                        foreach ($email_alert_1 as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Primera Alerta Incapacidades')
                                ->recipients($recipient)
                                ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>90</b> dias.")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->view('preventiveoccupationalmedicine.abssenteeism.notifyExpiredAbssen')
                                ->with(['data'=>$expired_email_1_alert])
                                ->company($company)
                                ->send();
                        }
                    } 
                }

                if (COUNT($expired_email_2_alert) > 0)
                {                                
                    $email_alert_2 = explode(',', $this->keys_absenteeism['users_notify_expired_absenteeism_expired_180']);

                    if (count($email_alert_2) > 0)
                    {
                        foreach ($email_alert_2 as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Segunda Alerta Incapacidades')
                                ->recipients($recipient)
                                ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>180</b> dias.")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->view('preventiveoccupationalmedicine.abssenteeism.notifyExpiredAbssen')
                                ->with(['data'=>$expired_email_2_alert])
                                ->company($company)
                                ->send();
                        }
                    } 
                }

                if (COUNT($expired_email_3_alert) > 0)
                {            
                    $email_alert_3 = explode(',', $this->keys_absenteeism['users_notify_expired_absenteeism_expired_540']);

                    if (count($email_alert_3) > 0)
                    {
                        foreach ($email_alert_3 as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Tercera Alerta Incapacidades')
                                ->recipients($recipient)
                                ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>$540</b> dias.")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->view('preventiveoccupationalmedicine.abssenteeism.notifyExpiredAbssen')
                                ->with(['data'=>$expired_email_3_alert])
                                ->company($company)
                                ->send();
                        }
                    } 
                }
            }

            \Log::info('Termino tarea ausentismo: '.Carbon::now());

            DB::commit();

        } catch(Exception $e) {
            DB::rollback();
            $this->respondHttp500();
        }
    }

    public function getConfig($company_id)
    {
        $valores = [
            "days_alert_expiration_date_absenteeism_90" => '',
            "users_notify_expired_absenteeism_expired_90" => '',
            "days_alert_expiration_date_absenteeism_180" => '',
            "users_notify_expired_absenteeism_expired_180" => '',
            "days_alert_expiration_date_absenteeism_540" => '',
            "users_notify_expired_absenteeism_expired_540" => '',
            'name_table_absenteeism' => '',
            'name_column_fec_ini_absenteeism' => '',
            'name_column_fec_fin_absenteeism' => '',
            'name_column_employee_name_absenteeism' => '',
            'name_column_employee_identification_absenteeism' => '',
            'name_column_cod_diag_absenteeism' => '',
            'name_column_employee_description_diag_absenteeism' => '',
        ];  
        $key_principal = "expired_absenteeism";
        $keys = [
            "days_alert_expiration_date_absenteeism_90",
            "users_notify_expired_absenteeism_expired_90",
            "days_alert_expiration_date_absenteeism_180",
            "users_notify_expired_absenteeism_expired_180",
            "days_alert_expiration_date_absenteeism_540",
            "users_notify_expired_absenteeism_expired_540",
            'name_table_absenteeism',
            'name_column_fec_ini_absenteeism',
            'name_column_fec_fin_absenteeism',
            'name_column_employee_name_absenteeism',
            'name_column_employee_identification_absenteeism',
            'name_column_cod_diag_absenteeism',
            'name_column_employee_description_diag_absenteeism',
        ];
        
        try
        {
            $exists = ConfigurationsCompany::company($company_id)->findByKey($key_principal);

            if ($exists && $exists == 'SI')
            {
                foreach ($keys as $key => $value) 
                {                    
                    $exists_key = ConfigurationsCompany::company($company_id)->findByKey($value);

                    if ($exists_key)
                        $valores[$value] = $exists_key;
                    else
                        $valores[$value] = $exists_key;
                }
            }

            return $valores;
            
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return [];
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
