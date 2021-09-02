<?php

namespace App\Jobs\IndustrialSecure\RiskMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixExcel;
use App\Facades\Mail\Facades\NotificationMail;

class RiskMatrixExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $risk_matrix_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $risk_matrix_id)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->risk_matrix_id = $risk_matrix_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/matriz_de_riesgos_'.date("YmdHis").'.xlsx';
      Excel::store(new RiskMatrixExcel($this->company_id, $this->risk_matrix_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de matriz de riesgos')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de matriz de riesgos.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('riskMatrix')
        ->event('Job: DangerMatrixExportJob')
        ->company($this->company_id)
        ->send();
    }
}
