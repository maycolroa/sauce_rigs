<?php

namespace App\Jobs\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\DangerousConditions\Inspections\InspectionExcel;
use App\Facades\Mail\Facades\NotificationMail;

class InspectionExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $filters;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $filters)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->filters = $filters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/inspecciones_'.date("YmdHis").'.xlsx';
      Excel::store(new InspectionExcel($this->company_id, $this->filters, $this->user),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de las inspecciones')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de inspecciones.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('dangerousConditions')
        ->event('Job: InspectionExportJob')
        ->company($this->company_id)
        ->send();
    }
}
