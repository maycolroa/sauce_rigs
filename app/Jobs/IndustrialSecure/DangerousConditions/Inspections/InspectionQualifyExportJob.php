<?php

namespace App\Jobs\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionCompletExcel;
use App\Facades\Mail\Facades\NotificationMail;

class InspectionQualifyExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $filters, $id)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/inspecciones_calificadas_'.date("YmdHis").'.xlsx';
      Excel::store(new InspectionCompletExcel($this->company_id, $this->filters, $this->id),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de las inspecciones calificadas')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de inspecciones.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('dangerousConditions')
        ->event('Job: InspectionQualifyExportJob')
        ->company($this->company_id)
        ->send();
    }
}
