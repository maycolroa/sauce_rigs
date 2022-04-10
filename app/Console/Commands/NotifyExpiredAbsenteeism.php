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
    protected $description = 'Envio de notificacion por vencimiento cercano de reposos de ausentismo, los dias anticipados seran configurados por el cliente';

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

                $records = DB::connection('ausentismo')
                ->table("ausentismo.Ausentismo_$company_get->id")
                ->whereDate('FechaFinal', '>', Carbon::now()->format('Y-m-d'))
                //->whereRaw("FechaFinal > CURDATE()")
                ->get();

                foreach ($records as $key => $record) 
                {
                    $dateExpired = Carbon::createFromFormat('Y-m-d H:i:s', $record->FechaFinal);

                $diff = $dateExpired->diffInDays(Carbon::now());

                    if ($diff <= $configDay)
                    {
                        if ($company == 18)
                        {
                            if ($record->CodigoDiagnostico =! 'COVID-19, VIRUS NO IDENTIFICADO')
                                $dx_18 = explode('-', $record->CodigoDiagnostico);
                            else
                                $dx_18 = explode(',', $record->CodigoDiagnostico);

                            $record->CodigoDiagnostico = $dx_18[0];
                            $record->NombreDiagnostico = $dx_18[1];
                        }

                        $content = [
                            'Identificacion' => $record->Identificacion,
                            'Nombre' => $record->Nombre,
                            'Codigo Diagnostico' => $record->CodigoDiagnostico,
                            'Nombre Diagnostico' => $record->NombreDiagnostico,
                            'Tipo Ausentismo' => $record->TipoAusentismo,
                            'Fecha Inicial' => $record->FechaInicial,
                            'Fecha Final' => $record->FechaFinal,
                            'Dias' => $record->Dias,
                            'Prorroga' => $record->IndicadorProrroga,
                        ];

                        array_push($expired, $content);
                    }
                }

                $expired_email = [];
                $responsibles_bbdd = ConfigurationsCompany::company($company)->findByKey('users_notify_expired_absenteeism_expired');
                $responsibles = [];

                if ($responsibles_bbdd)
                    $responsibles = explode(',', $responsibles_bbdd);

                foreach ($expired as $key => $value) {
                    $mail_send_exist = $this->checkSend($value['Identificacion'], $value['Codigo Diagnostico'], $company_get->id);

                    if ($mail_send_exist && COUNT($responsibles) > 0)
                    {
                        array_push($expired_email, $value);

                        $insert_email_record = new LogNotifyExpired;
                        $insert_email_record->company_id = $company;
                        $insert_email_record->document = $value['Identificacion'];
                        $insert_email_record->cod_dx = $value['Codigo Diagnostico'];
                        $insert_email_record->email_send = $responsibles_bbdd;
                        $insert_email_record->save();
                    }
                }

                if (COUNT($expired_email) > 0)
                {            
                    if (count($responsibles) > 0)
                    {
                        foreach ($responsibles as $email)
                        {
                            $recipient = new User(["email" => $email]); 

                            NotificationMail::
                                subject('Sauce - Ausentismos vencidos')
                                ->recipients($recipient)
                                ->message("Este es el listado de ausentismos que estan a <b>$configDay</b> dias de vencerse")
                                ->module('absenteeism')
                                ->event('Tarea programada: NotifyExpiredAbsenteeism')
                                ->table($expired_email)
                                ->company($company)
                                ->send();
                        }
                    } 
                }
            }

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
        
        try
        {
            $exists = ConfigurationsCompany::company($company_id)->findByKey($key);

            if ($exists && $exists == 'SI')
            {
                $days = ConfigurationsCompany::company($company_id)->findByKey($key1);
                return $days;
            }
            else
                return NULL;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    public function checkSend($ident, $cod_dx, $company_id)
    {
        $var = LogNotifyExpired::where('company_id', $company_id)
        ->where('document', $ident)->where('cod_dx', $cod_dx)->first();

        if ($var)
            return false;
        else
            return true;
    }
}
