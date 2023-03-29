<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\System\Licenses\License;
use App\Models\General\Company;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\LogNotifyExpired;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;
use App\Models\Administrative\Employees\Employee;
use Carbon\Carbon;
use DB;
use DateTime;

class NotifyIncapacitatedReinc extends Command
{

    protected $expired_email_1_alert = [];
    protected $expired_email_2_alert = [];           
    protected $expired_email_3_alert = [];  
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-incapacitated-reinc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de notificacion por dias de incapacidad superados en los reportes de reincorporaciones, los dias anticipados seran configurados por el cliente';

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
            \Log::info('Comenzo tarea reincorporaciones incapacidades: '.Carbon::now());

            DB::beginTransaction();

            $companies = License::selectRaw('DISTINCT company_id')
                ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
                ->withoutGlobalScopes()
                ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
                ->where('sau_license_module.module_id', '21');

            $companies = $companies->pluck('sau_licenses.company_id');

            foreach ($companies as $key => $company)
            {
                $expired = [];

                $company_get = Company::find($company);


                \Log::info($company_get);

                if ($company_get->id == 1 || $company_get->id == 130 || $company_get->id == 409)
                {

                    \Log::info('entro 2');
                    $configDay = $this->getConfig($company);

                    if (!$configDay)
                        continue;

                        \Log::info($company);

                    $responsibles_bbdd = ConfigurationsCompany::company($company)->findByKey('users_notify_incapacitated');

                    $checks = Check::withoutGlobalScopes()->where('company_id', $company)->get();
            
                    $responsibles = [];
                    $number_notification = 0;

                    foreach ($checks as $key => $check) 
                    {
                        if ($check->start_incapacitated)
                        {

                            \Log::info('entro 1');
                            $employee = Employee::withoutGlobalScopes()->find($check->employee_id);

                            $days = $this->timeDifferenceDays((Carbon::createFromFormat('Y-m-d', $check->start_incapacitated))->toDateString());

                            $content = [
                                'Identificacion' => $employee->identification,
                                'Nombre' => $employee->name,
                                'Codigo Diagnostico' => $check->cie10Code->code,
                                'Nombre Diagnostico' => $check->cie10Code->description,
                                'Fecha Inicial' => $check->start_incapacitated,
                                'Dias' => $days
                            ];


                            \Log::info($days);

                            if (COUNT($configDay) == 3)
                            {
                                if ($days >= $configDay[2])
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                                else if ($days >= $configDay[1])
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                                else if ($days >= $configDay[0])
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                            }
                            else if (COUNT($configDay) == 2)
                            {
                                if ($days >= $configDay[1])
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                                else if ($days >= $configDay[0])       
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                            }
                            else if (COUNT($configDay) == 1)
                            {
                                if ($days >= $configDay[0])       
                                    $this->verifyNotification($check->id, $content, $responsibles_bbdd, $company);
                            }
                        }
                        else
                        {
                            continue;
                        }
                    }

                    $responsibles = explode(',', $responsibles_bbdd);

                    if (COUNT($this->expired_email_1_alert) > 0)
                    {
                        if (count($responsibles) > 0)
                        {
                            foreach ($responsibles as $email)
                            {
                                $recipient = new User(["email" => $email]); 

                                NotificationMail::
                                    subject('Sauce - Primera Alerta Incapacidades')
                                    ->recipients($recipient)
                                    ->message("Este es el listado de incapacidades que cumplen o superan <b>$configDay[0]</b> dias.")
                                    ->module('absenteeism')
                                    ->event('Tarea programada: NotifyIncapacitatedReinc')
                                    ->view('preventiveoccupationalmedicine.reinstatements.notifyExpiredIncapacitated')
                                    ->with(['data'=>$this->expired_email_1_alert])
                                    ->company($company)
                                    ->send();
                            }
                        } 
                    }

                    if (COUNT($this->expired_email_2_alert) > 0)
                    {            
                        if (count($responsibles) > 0)
                        {
                            foreach ($responsibles as $email)
                            {
                                $recipient = new User(["email" => $email]); 

                                NotificationMail::
                                    subject('Sauce - Segunda Alerta Incapacidades')
                                    ->recipients($recipient)
                                    ->message("Este es el listado de incapacidades que cumplen o estan proximas a cumplir <b>$configDay[1]</b> dias.")
                                    ->module('absenteeism')
                                    ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                    ->view('preventiveoccupationalmedicine.reinstatements.notifyExpiredIncapacitated')
                                    ->with(['data'=>$this->expired_email_2_alert])
                                    ->company($company)
                                    ->send();
                            }
                        } 
                    }

                    if (COUNT($this->expired_email_3_alert) > 0)
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
                                    ->view('preventiveoccupationalmedicine.reinstatements.notifyExpiredIncapacitated')
                                    ->with(['data'=>$this->expired_email_3_alert])
                                    ->company($company)
                                    ->send();
                            }
                        } 
                    }
                }

            }

            \Log::info('Termino tarea reincorporaciones incapacidades: '.Carbon::now());

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

    public function checkSend($check_id, $company_id, $number)
    {
        $var = LogNotifyExpired::where('company_id', $company_id)->where('check_id', $check_id)->where('number_notification', $number)->first();

        if ($var)
            return false;
        else
            return true;
    }

    private function timeDifferenceDays($startDate, $endDate = null)
    {
      $start = new DateTime($startDate);
      $end;

      if ($endDate == null)
          $end = new DateTime();
      else
          $start = new DateTime($endDate);

      $interval = $start->diff($end);

      return $interval->format('%a');
    }

    public function verifyNotification($check_id, $content, $responsibles_bbdd, $company)
    {
        $notify_1_exist = $this->checkSend($check_id, $company, 1);
        $notify_2_exist = $this->checkSend($check_id, $company, 2);
        $notify_3_exist = $this->checkSend($check_id, $company, 3);

        if ($notify_1_exist)
        {
            if ($notify_2_exist)
            {
                if ($notify_3_exist)
                {
                    array_push($this->expired_email_3_alert, $content);

                    $insert_email_check = new LogNotifyExpired;
                    $insert_email_check->company_id = $company;
                    $insert_email_check->check_id = $check_id;
                    $insert_email_check->number_notification = 3;
                    $insert_email_check->email_send = $responsibles_bbdd;
                    $insert_email_check->save();
                }
            }
            else
            {
                array_push($this->expired_email_2_alert, $content);

                $insert_email_check = new LogNotifyExpired;
                $insert_email_check->company_id = $company;
                $insert_email_check->check_id = $check_id;
                $insert_email_check->number_notification = 2;
                $insert_email_check->email_send = $responsibles_bbdd;
                $insert_email_check->save();
            }
        }
        else
        {
            array_push($this->expired_email_1_alert, $content);

            $insert_email_check = new LogNotifyExpired;
            $insert_email_check->company_id = $company;
            $insert_email_check->check_id = $check_id;
            $insert_email_check->number_notification = 1;
            $insert_email_check->email_send = $responsibles_bbdd;
            $insert_email_check->save();
        }
    }
}
