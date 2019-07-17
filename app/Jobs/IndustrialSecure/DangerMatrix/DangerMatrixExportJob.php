<?php

namespace App\Jobs\IndustrialSecure\DangerMatrix;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndustrialSecure\DangerMatrix\DangerMatrixExcel;
use App\Facades\Mail\Facades\NotificationMail;

class DangerMatrixExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $danger_matrix_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $danger_matrix_id)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->danger_matrix_id = $danger_matrix_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $nameExcel = 'export/1/matriz_de_peligros_'.date("YmdHis").'.xlsx';
      Excel::store(new DangerMatrixExcel($this->company_id, $this->danger_matrix_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
      
      $paramUrl = base64_encode($nameExcel);
      
      NotificationMail::
        subject('Exportación de matriz de peligros')
        ->recipients($this->user)
        ->message('Se ha generado una exportación de matriz de peligros.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('dangerMatrix')
        ->event('Job: DangerMatrixExportJob')
        ->company($this->company_id)
        ->send();
    }
}
