<?php

namespace App\Jobs\IndustrialSecure\DangerMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixReportExcel;
use App\Facades\Mail\Facades\NotificationMail;

class DangerMatrixReportExportJob implements ShouldQueue
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
      $nameExcel = 'export/1/matriz_de_peligros_reporte_'.date("YmdHis").'.xlsx';
      Excel::store(new DangerMatrixReportExcel($this->company_id, $this->filters), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de matriz de peligros - Reportes')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de reportes de matriz de peligros.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('dangerMatrix')
        ->event('Job: DangerMatrixReportExportJob')
        ->company($this->company_id)
        ->send();
    }
}
