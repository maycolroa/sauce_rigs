<?php

namespace App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreventiveOccupationalMedicine\BiologicalMonitoring\RespiratoryAnalysis\RespiratoryAnalysisExcel;
use App\Facades\Mail\Facades\NotificationMail;

class RespiratoryAnalysisExportJob implements ShouldQueue
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
      $nameExcel = 'export/1/respiratorio_'.date("YmdHis").'.xlsx';
      Excel::store(new RespiratoryAnalysisExcel($this->company_id, $this->filters), $nameExcel,'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de los Análisis Respiratorio')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de Análisis Respiratorio.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('biologicalMonitoring/respiratoryAnalysis')
        ->event('Job: RespiratoryAnalysisExportJob')
        ->company($this->company_id)
        ->send();
    }
}
