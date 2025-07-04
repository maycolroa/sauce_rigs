<?php

namespace App\Jobs\LegalAspects\LegalMatrix\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\LegalMatrix\Reports\ReportLawExcel;
use App\Facades\Mail\Facades\NotificationMail;

class ReportLawExportJob implements ShouldQueue
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
      $nameExcel = 'export/1/matriz_legal_reporte_'.date("YmdHis").'.xlsx';
      Excel::store(new ReportLawExcel($this->company_id, $this->user, $this->filters),$nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación del reporte de matriz legal')
        ->recipients($this->user)
        ->message('Se ha generado una exportación del reporte de matriz legal.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('legalMatrix')
        ->event('Job: ReportLawExportJob')
        ->company($this->company_id)
        ->send();
    }
}
