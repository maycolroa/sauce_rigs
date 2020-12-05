<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\QualificationRepeat;
use Carbon\Carbon;
use App\Traits\LocationFormTrait;
use App\Traits\UtilsTrait;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\Administrative\Users\User;
use DB;

class RememberRepeatInspetion extends Command
{
    use LocationFormTrait, UtilsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remember-repeat-inspetion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de repeticion de inspeccion';

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
        $dayReference = 7;

        $now = Carbon::now();
        $dateReference = Carbon::now()->addDays($dayReference)->format('Y-m-d 00:00:00');;

        $repeat_notify = QualificationRepeat::select('sau_ph_inspection_qualification_repeat.*', 'sau_ph_inspections.*')
        ->join('sau_ph_inspections', 'sau_ph_inspections.id', 'sau_ph_inspection_qualification_repeat.inspection_id')
        ->where('repeat_date', $dateReference)->get();

        foreach ($repeat_notify as $key => $value)
        {
            $emails = [];

            if ($value->send_emails)
                $emails = explode(',', $value->send_emails);

            array_push($emails, $value->user->email);

            foreach ($emails as $email)
            {
                $recipient = new User(["email" => $email]); 

                NotificationMail::
                    subject('Inspecciones - Repetición de inspección')
                    ->recipients($recipient)
                    ->message('Estimado usuario, la siguiente inspección debe repetirse dentro de 7 dias.')
                    ->module('dangerousConditions')
                    ->event('Tarea programada: RememberRepeatInspetion')
                    ->list($this->prepareDataTable($value), 'ul')
                    ->company($value->company_id)
                    ->send();
            }
        }
    }

    private function prepareDataTable($data)
    {
        $keywords = $data->user->getKeywords($data->company_id);

        $location = $this->getLocationFormConfModule($data->company_id);

        $result = [];

        array_push($result, 'Usuario calificador: '.$data->user->name);

        array_push($result, 'Nombre de la inspección: '.$data->name);

        if ($data->fields_adds)
            array_push($result, 'Campos adicionales: '.$data->fields_adds);

        array_push($result, $keywords['regional'].' calificado: '.$data->regional);

        if ($location['headquarter'] == 'SI')
        {
            if ($data->headquarter)
                array_push($result, $keywords['headquarter'].' calificado: '.$data->headquarter);
        }

        if ($location['process'] == 'SI')
        {
            if ($data->process)
                array_push($result, $keywords['process'].' calificado: '.$data->process);
        }

        if ($location['area'] == 'SI')
        {
            if ($data->area)
                array_push($result, $keywords['area'].' calificado: '.$data->area);
        }
        
        return $result;
    }
}
