<?php

namespace App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryExcel;
use App\Facades\Mail\Facades\NotificationMail;

class AudiometryExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $audiometries = Audiometry::select(
        'bm_audiometries.*',
        'sau_employees.identification as employee_identification',
        'sau_employees.name as employee_name'
      )->join('sau_employees','sau_employees.id','bm_audiometries.employee_id')
      ->join('sau_employees_regionals','sau_employees_regionals.id','sau_employees.employee_regional_id');

      $nameExcel = 'export/1/audiometrias_'.date("YmdHis").'.xlsx';
      Excel::store(new AudiometryExcel($audiometries->get()),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);

      NotificationMail::
        subject('Exportación de las audiometrias')
        ->recipients(Auth::user())
        ->message('Se ha generado una exportación de audiometrias.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('biologicalMonitoring/audiometry')
        ->send();
    }
}
