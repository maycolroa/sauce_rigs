<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Illuminate\Database\Eloquent\Collection;
use App\User;

class AudiometryNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiometry-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta las audiometrias del dia anterior para saber cual empleado empeoro y notificar a la persona encargada';

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
        $audiometries = Audiometry::select(
            'bm_audiometries.*',
            'sau_employees.identification as employee_identification',
            'sau_employees.name as employee_name',
            'sau_employees.company_id as company_id',
            'sau_employees.email as email'
          )->join('sau_employees','sau_employees.id','bm_audiometries.employee_id')
          ->withoutGlobalScopes()
          ->whereRaw('bm_audiometries.date = DATE_ADD(CURDATE(), INTERVAL -1 DAY)')
          ->where('base_type_air', 'Base')->get();

          
        if (!$audiometries->isEmpty())
        {
            $data = [];

            foreach ($audiometries as $key => $value)
            {
                $audiometry_count = Audiometry::where('employee_id', $value->employee_id)->withoutGlobalScopes()->count();

                if ($audiometry_count > 1)
                {
                    $data[$value->company_id][] = $value;
                }
            }

            if (COUNT($data) > 0)
            {
                foreach ($data as $key => $value)
                {
                    $recipients = User::select('sau_users.email')
                                ->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                                ->where('sau_company_user.company_id', $key)->get();
                    
                    $nameExcel = 'export/1/audiometrias_notificacion_'.date("YmdHis").'.xlsx';
                    Excel::store(new AudiometryExcel(new Collection($value)),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
                    
                    $paramUrl = base64_encode($nameExcel);
                
                    NotificationMail::
                        subject('Notificación de las audiometrias')
                        ->recipients($recipients)
                        ->message('Lista de empleados que sufrierón una degradación en los resultados de sus audiometrias para el dia de ayer')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('biologicalMonitoring/audiometry')
                        ->send();
                }
            }
        }
    }
}
